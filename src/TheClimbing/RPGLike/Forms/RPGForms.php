<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Forms;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use jojoe77777\FormAPI\SimpleForm;

use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Skills\BaseSkill;
use TheClimbing\RPGLike\Utils;

class RPGForms
{
    private static RPGLike $main;

    public function __construct(RPGLike $rpg)
    {
        self::$main = $rpg;
    }

    public static function upgradeStatsForm(RPGPlayer $player, int $spleft)
    {
        if ($player->getSPleft() > 0) {
            $spleft += $player->getSPleft();
            $player->setSPleft(0);
        }
        if ($spleft <= 0) {
            $player->setSPleft(0);
            self::statsForm($player);
            return;
        }
        $messages = self::parseMessages($player, 'UpgradeForm', $spleft);

        $form = new SimpleForm(function (Player $pl, $data) use ($player, $spleft) {
            switch ($data) {
                case "strength":
                    $player->setSTR($player->getSTR() + 1);
                    $spleft--;
                    break;
                case "vitality":
                    $player->setVIT($player->getVIT() + 1);
                    $spleft--;
                    $player->applyVitalityBonus();
                    break;
                case "defense":
                    $player->setDEF($player->getDEF() + 1);
                    $spleft--;
                    break;
                case "dexterity":
                    $player->setDEX($player->getDEX() + 1);
                    $spleft--;
                    $player->applyDexterityBonus();
                    break;
                case "exit":
                default:
                    $player->setSPleft($spleft);
                    return;
            }
            self::upgradeStatsForm($player, $spleft);

        });

        $form->setTitle($messages['title']);
        $form->setContent($messages['content']);
        foreach ($messages['buttons'] as $key => $button) {
            $form->addButton($button, -1, '', $key);
        }
        $player->sendForm($form);
    }

    public static function skillUnlockForm(RPGPlayer $player, ?BaseSkill $skill)
    {
        $skillDescription = Utils::parseArrayKeywords(self::$main->consts, $skill->getDescription());
        $form = new SimpleForm(function () {
        });
        $title = $skill->getTitle();
        str_replace('{SKILLNAME}', ' ' . $skill->getName() . ' ', $title);
        $form->setTitle($title);
        $form->setContent($skillDescription['description'] . TextFormat::EOL . $skillDescription['unlocks']);
        $player->sendForm($form);
    }

    public static function skillHelpForm(RPGPlayer $player, ?BaseSkill $skill)
    {
        $skillDescription = Utils::parseArrayKeywords(self::$main->consts, $skill->getDescription());
        $form = new SimpleForm(function (Player $pl, $data) use ($player) {
            switch ($data) {
                case 'Back':
                    self::skillsHelpForm($player);
                    break;
            }
        });
        $title = $skill->getTitle();
        str_replace('{SKILLNAME}', ' ' . $skill->getName() . ' ', $title);
        $form->setTitle($title);
        $form->setContent($skillDescription['description']);
        $form->addButton('Back to menu', -1, '', 'Back');
        $player->sendForm($form);
    }

    public static function statsForm(RPGPlayer $player, bool $back = false)
    {
        $messages = self::parseMessages(($player), 'StatsForm');

        $form = new SimpleForm(function (Player $p, $data) use ($player) {
            if ($data == 'back') {
                self::menuForm($player);
            }
        });
        $form->setTitle($messages['title']);
        $form->setContent($messages['content']);
        foreach ($messages['buttons'] as $key => $message) {
            $form->addButton($message, -1, '', $key);
        }
        if ($back) {
            $form->addButton('Back', -1, '', 'back');
        }
        $player->sendForm($form);

    }

    public static function menuForm(RPGPlayer $player)
    {
        $menuStrings = self::parseMessages($player, 'MenuForm');
        $form = new SimpleForm(function (Player $pl, $data) use ($player) {
            switch ($data) {
                case "skills":
                    self::skillsHelpForm($player);
                    break;
                case "stats":
                    self::statsForm($player, true);
                    break;
                case "upgrade":
                    self::upgradeStatsForm($player, 0);
                    break;
            }
        });
        $form->setTitle($menuStrings['title']);
        foreach ($menuStrings['buttons'] as $key => $buttonText) {
            $form->addButton($buttonText, -1, '', $key);
        }
        if (array_key_exists('content', $menuStrings)) {
            $form->setContent($menuStrings['content']);
        } else {
            self::$main->getLogger()->alert('Please check messages.yml Menu Form contents are empty');
        }
        $player->sendForm($form);
    }

    public static function skillsHelpForm(RPGPlayer $player)
    {
        $skills = $player->getSkills();
        $form = new SimpleForm(function (Player $pl, $data) use ($skills, $player) {
            foreach ($skills as $skill) {
                if ($skill == $data) {
                    self::skillHelpForm($player, $skill);
                }
            }
            if ($data == 'back') {
                self::menuForm($player);
            }
        });
        $form->setTitle("All available skills");
        foreach ($skills as $skill) {
            $form->addButton($skill, -1, '', $skill);
        }
        $form->addButton("Back to main menu", -1, '', 'back');
        $player->sendForm($form);
    }

    public static function parseMessages(RPGPlayer $player, string $type, int $spleft = 0): array
    {
        $messages = self::$main->getMessages()['Forms'][$type];
        $stats = $player->getAttributes();
        $stats['PLAYER'] = $player->getName();
        $stats['SPLEFT'] = $spleft;

        $joined = array_merge($stats, self::$main->consts);
        return Utils::parseArrayKeywords($joined, $messages);
    }
}