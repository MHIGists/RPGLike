<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike;
    
    
    use function str_repeat;
    
    use pocketmine\plugin\PluginBase;
    
    use pocketmine\utils\TextFormat;
    use pocketmine\utils\Config;
    
    use TheClimbing\RPGLike\CustomCommands\LevelUpCommand;
    use TheClimbing\RPGLike\Skills\Tank;


    class Main extends PluginBase
    {
        
        public $globalMessages = [];
        public $consts;
        private $rpg ;
        
        public function onLoad ()
        {
            $this->saveDefaultConfig();
            $this->saveResource('messages.yml');
            $this->getMessages();
            $this->getMessages();
            $this->setConsts();
            
            $this->getLogger()->notice(TextFormat::RED . "Loaded successfully!");
        }
        
        public function onEnable ()
        {
            $this->rpg = new RPGLike($this);
            
            $command = new LevelUpCommand($this);
            $this->getServer()->getCommandMap()->register('levelup', $command);
            
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this->rpg), $this);
            
            $this->getLogger()->notice(TextFormat::RED . "Enabled successfully");
        }
        
        public function getMessages (): void
        {
            $messages = (new Config($this->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
            $this->globalMessages = $messages;
        }
        
        public function setConsts (): void
        {
            $this->consts = [
                "MOTD" => $this->getServer()->getMotd(),
                "10SPACE" => str_repeat(" ", 10),
                "20SPACE" => str_repeat(" ", 20),
                "30SPACE" => str_repeat(" ", 30),
                "40SPACE" => str_repeat(" ", 40),
                "50SPACE" => str_repeat(" ", 50),
                "NL" => "\n",
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
        
        
        
        public function onDisable ()
        {
            $this->rpg->saveVars();
        }
    }
