<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike;
    
    use pocketmine\entity\Attribute;
    use pocketmine\plugin\PluginBase;
    use pocketmine\utils\Config;
    use pocketmine\utils\TextFormat;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Commands\LevelUpCommand;
    use TheClimbing\RPGLike\Forms\RPGForms;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Commands\RPGCommand;
    use TheClimbing\RPGLike\Players\RPGPlayer;
    use TheClimbing\RPGLike\Skills\SkillsManager;


    class RPGLike extends PluginBase
    {
        private static $instance;

        public $messages;
        public $consts = [];
        public $skillUnlocks = [];

        public function onLoad()
        {

            
            $this->saveDefaultConfig();
            $this->saveResource('messages.yml');
            $this->setConsts();

            $messages = (new Config($this->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
            $this->messages = $messages;

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
        }
        
        
        
        public function setConsts() : void
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

        public function getSkillUnlocks() : void
        {
            $this->skillUnlocks = $this->getConfig()->getNested('SkillUpgrades');
        }

        


        public function getMessages() : array
        {
            return $this->messages;
        }

        public static function getInstance() : RPGLike
        {
            return self::$instance;
        }
    }