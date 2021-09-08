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

    public function onLoad() : void
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

    public function onEnable() : void
    {
        $rpg = new RPGCommand($this);
        $this->getServer()->getCommandMap()->register('rpg', $rpg);

        $lvl = new LevelUpCommand();
        $this->getServer()->getCommandMap()->register('lvlup', $lvl);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        self::$instance = $this;

        if ($this->config['Hud']['on'] == true) {
            $this->getScheduler()->scheduleRepeatingTask(new HudTask($this), $this->config['Hud']['period'] * 20);
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
            'LEVEL' => $player->getWorld()->getDisplayName(),
            'XPLEVEL' => $player->getXpManager()->getXpLevel(),
            'TIME' => date('h:i')
        ];
        $keywords = array_merge($playerSpecific, $playerSpecific);
        return Utils::parseKeywords($keywords, $this->config['Hud']['message']);

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