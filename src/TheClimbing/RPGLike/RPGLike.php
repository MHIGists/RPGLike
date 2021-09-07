<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use TheClimbing\RPGLike\Commands\LevelUpCommand;
use TheClimbing\RPGLike\Commands\RPGCommand;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\Tasks\HudTask;


class RPGLike extends PluginBase
{
    private static RPGLike $instance;

    public array $messages;
    public $config;
    public array $consts = [];
    public array $skillUnlocks = [];

    private $skills;

    public function onLoad()
    {
        $this->saveDefaultConfig();
        $this->saveResource('messages.yml');
        $this->setConsts();

        $messages = (new Config($this->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
        $this->messages = $messages;

        $this->config = $this->getConfig()->getAll();
        date_default_timezone_set($this->config['Hud']['timezone']);
        new RPGForms($this);
    }

    public function onEnable()
    {
        $rpg = new RPGCommand($this);
        $this->getServer()->getCommandMap()->register('rpg', $rpg);

        $lvl = new LevelUpCommand();
        $this->getServer()->getCommandMap()->register('lvlup', $lvl);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getSkillUnlocks();
        self::$instance = $this;

        if ($this->config['Hud']['on'] == true) {
            $this->getScheduler()->scheduleRepeatingTask(new HudTask($this), $this->config['Hud']['period'] * 20);
        }
        foreach ($this->config['Skills'] as $key => $skill) {
            $this->registerSkill($key, $skill);
        }
    }


    public function setConsts(): void
    {
        $this->consts = [
            "MOTD" => $this->getServer()->getMotd(),
            "10SPACE" => str_repeat(" ", 10),
            "20SPACE" => str_repeat(" ", 20),
            "30SPACE" => str_repeat(" ", 30),
            "40SPACE" => str_repeat(" ", 40),
            "50SPACE" => str_repeat(" ", 50),
            "NL" => TextFormat::EOL,
            "BLACK" => TextFormat::BLACK,
            "DARK_BLUE" => TextFormat::DARK_BLUE,
            "DARK_GREEN" => TextFormat::DARK_GREEN,
            "DARK_AQUA" => TextFormat::DARK_AQUA,
            "DARK_RED" => TextFormat::DARK_RED,
            "DARK_PURPLE" => TextFormat::DARK_PURPLE,
            "GOLD" => TextFormat::GOLD,
            "GRAY" => TextFormat::GRAY,
            "DARK_GRAY" => TextFormat::DARK_GRAY,
            "BLUE" => TextFormat::BLUE,
            "GREEN" => TextFormat::GREEN,
            "AQUA" => TextFormat::AQUA,
            "RED" => TextFormat::RED,
            "LIGHT_PURPLE" => TextFormat::LIGHT_PURPLE,
            "YELLOW" => TextFormat::YELLOW,
            "WHITE" => TextFormat::WHITE,
            "OBFUSCATED" => TextFormat::OBFUSCATED,
            "BOLD" => TextFormat::BOLD,
            "STRIKETHROUGH" => TextFormat::STRIKETHROUGH,
            "UNDERLINE" => TextFormat::UNDERLINE,
            "ITALIC" => TextFormat::ITALIC,
            "RESET" => TextFormat::RESET,
        ];
    }

    public function getHUD(RPGPlayer $player): string
    {
        $item = $player->getInventory()->getItemInHand();

        $playerSpecific = [
            'HEALTH' => $player->getHealth(),
            'MAXHP' => $player->getMaxHealth(),
            'DAMAGE' => $item->getAttackPoints() + round($player->getSTRBonus(), 0, PHP_ROUND_HALF_UP),
            'MOVEMENTSPEED' => round($player->getMovementSpeed(), 4, PHP_ROUND_HALF_UP),
            'ABSORPTION' => $player->getAbsorption() + $player->getDEFBonus(),
            'TICKS' => $player->getServer()->getTicksPerSecondAverage(),
            'LEVEL' => $player->getLevel()->getName(),
            'XPLEVEL' => $player->getXpLevel(),
            'TIME' => date('h:i')
        ];
        $keywords = array_merge($playerSpecific, $playerSpecific);
        return Utils::parseKeywords($keywords, $this->config['Hud']['message']);

    }

    public function registerSkill(string $skillName, array $values): void
    {
        $this->skills[$skillName] = $values;

        if (array_key_exists("namespace", $values)) {
            if (!empty($values['namespace'])) {
                $this->skills[$skillName]['namespace'] = $values['namespace'];
            }
        }
        if (array_key_exists($skillName, $this->getMessages()['Skills'])) {
            $this->skills[$skillName]["description"] = $this->getMessages()['Skills'][$skillName];
        }
    }

    public function getSkillDescription(string $skillName)
    {
        return $this->skills[$skillName]['description'];
    }

    public function getAvailableSkills(): array
    {
        return array_keys($this->skills);
    }


    public function getSkillUnlocks(): void
    {
        $this->skillUnlocks = $this->config['SkillUpgrades'];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public static function getInstance(): RPGLike
    {
        return self::$instance;
    }
}