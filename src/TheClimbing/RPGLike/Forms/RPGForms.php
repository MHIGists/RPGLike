<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Forms;

use jojoe77777\FormAPI\SimpleForm;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Skills\BaseSkill;
use TheClimbing\RPGLike\Traits\BaseTrait;
use TheClimbing\RPGLike\Utils;

class RPGForms
{
    private static RPGLike $main;
    //TODO add more check for array keys to ensure no server braking errors occur
    public function __construct(RPGLike $rpg)
    {
        self::$main = $rpg;
    }

    public static function upgradeStatsForm(RPGPlayer $player)
    {
        if ($player->spleft <= 0) {
            $player->setSPleft(0);
            self::statsForm($player);
            $player->checkForSkills();
            if ($player->getSkill('Tank')->isUnlocked()) {
                $player->getSkill('Tank')->passiveEffect($player);
            }
            if ($player->getSkill('Fortress')->isUnlocked()) {
                $player->getSkill('Fortress')->passiveEffect($player);
            }
            $player->checkSkillLevel();
            $player->setSPleft($player->spleft);
            return;
        }
        $messages = self::parseMessages($player, 'UpgradeForm', $player->spleft);

        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($player) {
            switch ($data) {
                case "strength":
                    $player->setSTR($player->getSTR() + 1);
                    $player->spleft--;
                    break;
                case "vitality":
                    $player->setVIT($player->getVIT() + 1);
                    $player->spleft--;
                    $player->applyVitalityBonus();
                    break;
                case "defense":
                    $player->setDEF($player->getDEF() + 1);
                    $player->spleft--;
                    break;
                case "dexterity":
                    $player->setDEX($player->getDEX() + 1);
                    $player->spleft--;
                    break;
                case "exit":
                default:
                    return;
            }
            self::upgradeStatsForm($player);

        });

        $form->setTitle($messages['title']);
        $form->setContent($messages['content']);
        foreach ($messages['buttons'] as $key => $button) {
            $form->addButton($button, -1, '', $key);
        }
        $player->sendForm($form);
    }

    public static function skillHelpForm(RPGPlayer $player, ?BaseSkill $skill)
    {
        $messages = $skill->getMessages()['SkillHelpForm'];
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($player) {
            if ($data == 'Back') {
                self::skillsHelpForm($player);
            }
        });
        $form->setTitle($skill->getName() . $messages['title']);
        $form->setContent($messages['content']);
        $form->addButton($messages['back_button'], -1, '', 'Back');
        $player->sendForm($form);
    }

    public static function statsForm(RPGPlayer $player, bool $back = false)
    {
        $messages = self::parseMessages(($player), 'StatsForm');

        $form = new SimpleForm(function (RPGPlayer $p, $data) use ($player) {
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
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($player) {
            switch ($data) {
                case "skills":
                    self::skillsHelpForm($player);
                    break;
                case "stats":
                    self::statsForm($player, true);
                    break;
                case "upgrade":
                    self::upgradeStatsForm($player);
                    break;
                case "traits":
                    self::traitsForm($player);
            }
        });
        $form->setTitle($menuStrings['title']);
        foreach ($menuStrings['buttons'] as $key => $buttonText) {
            $form->addButton($buttonText, -1, '', $key);
        }
        if (array_key_exists('content', $menuStrings)) {
            $form->setContent($menuStrings['content']);
        }
        $player->sendForm($form);
    }

    public static function skillsHelpForm(RPGPlayer $player)
    {
        $skills = $player->getSkills();
        $messages = self::parseMessages($player, 'SkillsHelpForm');
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($skills, $player) {
            foreach ($skills as $key => $skill) {
                if ($key == $data) {
                    self::skillHelpForm($player, $skill);
                }
            }
            if ($data == 'back') {
                self::menuForm($player);
            }
        });
        $form->setTitle($messages['title']);
        foreach ($skills as $key => $skill) {
            if (!$skill->isUnlocked()){
                continue;
            }
            $form->addButton($key, -1, '', $key);
        }
        $form->addButton($messages['buttons']['back'], -1, '', 'back');
        $player->sendForm($form);
    }
    public static function welcomeForm(RPGPLayer $player){
        $messages = self::parseMessages($player, 'WelcomeForm');
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($player){});
        $form->setTitle($messages['title']);
        $form->setContent($messages['content']);
        $player->sendForm($form);
    }
    public static function traitsForm(RPGPlayer $player){
        $traits = $player->getTraits();
        $messages = self::parseMessages($player, 'TraitsForm');
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($traits, $player){
            foreach ($traits as $key => $trait) {
                if ($key == $data){
                    self::traitHelpForm($player, $trait);
                }
            }
            if ($data == 'back') {
                self::menuForm($player);
            }
        });
        $form->setTitle($messages['title']);
        foreach ($traits as $key => $trait) {
            if($trait->isUnlocked()){
                $form->addButton($key,-1,'', $key);
            }
        }
        $form->addButton($messages['buttons']['back'],-1,'','back');
        $player->sendForm($form);
    }
    public static function traitHelpForm(RPGPlayer $player, BaseTrait $trait){
        $messages = self::parseMessages($player,'TraitHelpForm');
        $form = new SimpleForm(function (RPGPlayer $pl, $data) use ($player) {
            if ($data == 'Back') {
                self::traitsForm($player);
            }
        });
        $form->setTitle($trait->getName() . $messages['title']);
        $form->setContent($messages['content']);
        $form->addButton($messages['buttons']['back'], -1, '', 'Back');
        $player->sendForm($form);
    }

    public static function parseMessages(RPGPlayer $player, string $type, int $spleft = 0): array
    {
        $messages = self::$main->getMessages()->get('Forms')[$type];
        $stats = $player->getAttributes();
        $stats['PLAYER'] = $player->getName();
        $stats['SPLEFT'] = $spleft;

        $joined = array_merge($stats, self::$main->consts);
        return Utils::parseArrayKeywords($joined, $messages);
    }
}