<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike;
    
    use pocketmine\Player;
    use pocketmine\entity\Attribute;
    use pocketmine\plugin\PluginBase;
    use pocketmine\utils\Config;
    use pocketmine\utils\TextFormat;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Commands\LevelUpCommand;
    use TheClimbing\RPGLike\Forms\RPGForms;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Commands\RPGCommand;
    use TheClimbing\RPGLike\Skills\SkillsManager;


    class RPGLike extends PluginBase
    {
        private static $instance;

        public $messages;
        public $globalModifiers = [];
        public $consts = [];
        public $defaultModifiers = ['strModifier' => 0.15, 'vitModifier' => 0.175, 'defModifier' => 0.1, 'dexModifier' => 0.0002,];
        
        public function onLoad()
        {
            self::$instance = $this;
            
            $this->saveDefaultConfig();
            $this->saveResource('messages.yml');
            $this->setConsts();

            $messages = (new Config($this->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
            $this->messages = $messages;

            new RPGForms();
            new PlayerManager();
            new SkillsManager($this);
        }
        
        public function onEnable()
        {
            $rpg = new RPGCommand($this);
            $this->getServer()->getCommandMap()->register('rpg', $rpg);

            $lvl = new LevelUpCommand();
            $this->getServer()->getCommandMap()->register('lvlup', $lvl);
            
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
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
        
        public function getModifiers() : array
        {
            $modifiers = $this->getConfig()->getNested('modifiers');
            if($modifiers == null) {
                $this->getConfig()->setNested('modifiers', $this->defaultModifiers);
                $this->getConfig()->save();
                $this->globalModifiers = $this->defaultModifiers;
            } else {
                $this->globalModifiers = $modifiers;
            }
            return $this->globalModifiers;
        }
        
        public function applyDamageBonus(EntityDamageByEntityEvent $event) : void
        {
            $damager = $event->getDamager();
            if($damager instanceof Player) {
                $baseDamage = $event->getBaseDamage();
                $event->setBaseDamage($baseDamage + PlayerManager::getPlayer($damager->getName())->getSTRBonus());
            }
        }
        
        public function applyVitalityBonus(Player $player)
        {
            $playerName = $player->getName();
            $player->setMaxHealth(20 + PlayerManager::getPlayer($playerName)->getVITBonus());
            $player->setHealth(20 + PlayerManager::getPlayer($playerName)->getVITBonus());
        }
        
        public function applyDefenseBonus(EntityDamageByEntityEvent $event) : void
        {
            $receiver = $event->getEntity();
            if($receiver instanceof Player) {
                $receiver->setAbsorption($receiver->getAbsorption() + PlayerManager::getPlayer($receiver->getName())->getDEFBonus());
            }
        }
        
        public function applyDexterityBonus(Player $player)
        {
            $playerName = $player->getName();
            $dex = PlayerManager::getPlayer($playerName)->getDEXBonus();
            $movement = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
            $movement->setValue($movement->getValue() * (1 + $dex));
        }
        
        public function savePlayerVariables(string $playerName) : void
        {
            $player = PlayerManager::getPlayer($playerName);
            $playerVars = [
                'attributes' => $player->getAttributes(),
                'skills' => $player->getSkillNames(),
                'spleft' => $player->getSPleft(),
                'level' => $player->getLevel(),
                ];
            $players = $this->getConfig()->getNested('Players');
            $players[$playerName] = $playerVars;
            $this->getConfig()->setNested('Players', $players);
            $this->getConfig()->save();
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