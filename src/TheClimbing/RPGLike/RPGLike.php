<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike;

use pocketmine\item\Sword;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use TheClimbing\RPGLike\Commands\LevelUpCommand;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\PlayerManager;
use TheClimbing\RPGLike\Commands\RPGCommand;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\Skills\SkillsManager;
use TheClimbing\RPGLike\Tasks\HudTask;


class RPGLike extends PluginBase
{
    private static $instance;

    public $messages;
    public $config;
    public $consts = [];
    public $skillUnlocks = [];

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
        new PlayerManager($this);
        new SkillsManager($this);
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
            $this->getScheduler()->scheduleRepeatingTask(new HudTask($this), $this->config['Hud']['period']);
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

    public function getHUD(Player $player)
    {
        $string = Utils::parseKeywords($this->consts, $this->config['Hud']['message']);
        $item = $player->getInventory()->getItemInHand();
        $swordDamage = 1;
        if ($item instanceof Sword) {
            $swordDamage = $item->getAttackPoints();
        }
        $playerSpecific = [
            'HEALTH' => $player->getHealth(),
            'MAXHP' => $player->getMaxHealth(),
            'DAMAGE' => $swordDamage + round($player->getSTRBonus(), 0, PHP_ROUND_HALF_UP),
            'MOVEMENTSPEED' => round($player->movementSpeed, 4, PHP_ROUND_HALF_UP),
            'ABSORPTION' => $player->getArmorPoints() + $player->getDEFBonus(),
            'TICKS' => $player->getServer()->getTicksPerSecondAverage(),
            'LEVEL' => $player->getLevel()->getName(),
            'XPLEVEL' => $player->getXpLevel(),
            'TIME' => date('h:i')
        ];
        $string = Utils::parseKeywords($playerSpecific, $string);
        return $string;
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