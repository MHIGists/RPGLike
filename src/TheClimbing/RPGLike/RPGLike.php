<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike;
    
    use function array_merge;
    use function str_replace;
    
    use pocketmine\Player;
    use pocketmine\entity\Attribute;
    use pocketmine\plugin\PluginBase;
    use pocketmine\utils\TextFormat;
    use pocketmine\utils\Config;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Players\RPGPlayer;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Commands\RPGCommand;
    
    use jojoe77777\FormAPI\SimpleForm;
    
    class RPGLike extends PluginBase
    {
        private static $instance;
        
        private $playerManager;
        
        public $globalModifiers = [];
        public $globalMessages = [];
        public $consts = [];
        public $defaultStats = ['STR' => 1, 'VIT' => 1, 'DEF' => 1, 'DEX' => 1,];
        public $defaultModifiers = ['strModifier' => 0.15, 'vitModifier' => 0.175, 'defModifier' => 0.1, 'dexModifier' => 0.0002,];
        
        public function onLoad()
        {
            self::$instance = $this;
            
            $this->saveDefaultConfig();
            $this->saveResource('messages.yml');
            $this->getMessages();
            $this->setConsts();
            
            $this->playerManager = new PlayerManager($this);
        }
        
        public function onEnable()
        {
            $rpg = new RPGCommand($this);
            $this->getServer()->getCommandMap()->register('rpg', $rpg);
            
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        }
        
        public function getMessages() : void
        {
            $messages = (new Config($this->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
            $this->globalMessages = $messages;
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
                "NL" => "\n", "BLACK" => TextFormat::BLACK,
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
        
        public function upgradeStatsForm(RPGPlayer $player, int $spleft)
        {
            if($spleft <= 0) {
                $this->statsForm($player);
                return;
            }
            $messages = $this->parseMessages($player->getName(), $spleft);
            
            $form = new SimpleForm(function(Player $pl, $data) use ($player, $spleft) {
                switch($data) {
                    case "Strength":
                        $player->setSTR($player->getSTR() + 1);
                        $player->calcSTRBonus();
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Vitality":
                        $player->setVIT($player->getVIT() + 1);
                        $player->calcVITBonus();
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        $this->applyVitalityBonus($pl);
                        break;
                    case "Defense":
                        $player->setDEF($player->getDEF() + 1);
                        $player->calcDEFBonus();
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Dexterity":
                        $player->setDEX($player->getDEX() + 1);
                        $player->calcDEXBonus();
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        $this->applyDexterityBonus($pl);
                        break;
                    case "Exit":
                        break;
                }
            });
            
            $form->setTitle($messages['FormTitle']);
            $form->setContent($messages['FormContent']);
            foreach($messages['Buttons'] as $key => $button) {
                $form->addButton($button, -1, '', $key);
            }
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
            $player->checkForSkills();
        }
        
        public function descriptionSkillForm(Player $player, array $skillDescription)
        {
            
            $form = new SimpleForm(function(Player $player, $data) {
                switch($data) {
                    case 'Exit':
                        break;
                }
            });
            $form->setTitle($skillDescription['title']);
            $form->setContent($skillDescription['content']);
            $form->addButton($skillDescription['exitButton']);
            $player->sendForm($form);
            
        }
        
        public function statsForm(RPGPlayer $player)
        {
            $messages = $this->parseMessages($player->getName(), 0, true);
            
            $form = new SimpleForm(function(Player $player, $data) {
                switch($data) {
                    case "Exit":
                        break;
                }
            });
            
            $form->setTitle($messages['FormTitle']);
            $form->setContent($messages['FormContent']);
            foreach($messages['Buttons'] as $key => $message) {
                $form->addButton($message, -1, '', $key);
            }
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
            
        }
        public function RPGMenuForm(RPGPlayer $player)
        {
        
        }
        
        public function skillsForm(RPGPlayer $player)
        {
        
        }
        public function helpForm(RPGPlayer $player)
        {
        
        }
        
        public function parseMessages(string $playerName, int $spleft, bool $stats = false) : array
        {
            if($stats == true) {
                $messages = $this->globalMessages['StatsForm'];
            } else {
                $messages = $this->globalMessages['UpgradeForm'];
            }
            $stats = PlayerManager::getPlayer($playerName)->getAttributes();
            $stats['PLAYER'] = $playerName;
            $stats['SPLEFT'] = $spleft;
            
            $joined = array_merge($stats, $this->consts);
            
            $messages['FormContent'] = $this->parseKeywords($joined, $messages['FormContent']);
            
            return $messages;
        }
        
        public function parseKeywords(array $keywords, string $subject) : string
        {
            $subject = str_replace(['{', '}'], [' ', ' '], $subject);
            foreach($keywords as $key => $value) {
                $subject = str_replace($key, $value, $subject);
            }
            return $subject;
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
            $player = [
                'attributes' => PlayerManager::getPlayer($playerName)->getAttributes(),
                'skills' => $player->getSkillNames(),
                'level' => PlayerManager::getServerPlayer($playerName)->getXpLevel(),
                ];
            if($this->getConfig()->getNested($playerName) == null){
                $players = $this->getConfig()->getNested('players');
                $players[$playerName] = $player;
                $this->getConfig()->setNested('players', $players);
            }else
            {
                $this->getConfig()->setNested($playerName, $player);
            }
            $this->getConfig()->save();
        }
        
        public static function getInstance() : RPGLike
        {
            return self::$instance;
        }
    }