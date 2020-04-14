<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike;
    
    use function array_merge;
    use function array_key_exists;
    use function str_replace;
    
    use pocketmine\Player;
    use pocketmine\entity\Attribute;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Skills\SkillsManager;
    
    use jojoe77777\FormAPI\SimpleForm;
    
    class RPGLike
    {
        private static $instance;
        
        private $main;
        private $players = [];
        private $config;
        private $skillsManager;
        
        public $globalModifiers = [];
        public $defaultStats = ['STR' => 1, 'VIT' => 1, 'DEF' => 1, 'DEX' => 1,];
        public $defaultModifiers = ['STR' => 0.15, 'VIT' => 0.2, 'DEF' => 0.1, 'DEX' => 0.005,];
        
        public function __construct(Main $main)
        {
            self::$instance = $this;
            $this->main = $main;
            $this->config = $main->getConfig();
            $this->setPlayersFromConfig();
            $this->setModifiersFromConfig();
            $this->skillsManager = new SkillsManager($this);
            
        }
        
        private function setPlayersFromConfig() : void
        {
            $conf = $this->config;
            $array = $conf->getNested('players');
            if($array !== null) {
                $this->players = $array;
            }
        }
        
        private function setModifiersFromConfig() : void
        {
            $modifiers = $this->config->get('modifiers');
            if($modifiers === false) {
                $this->config->setNested('modifiers', $this->defaultModifiers);
                $this->config->save();
                $this->globalModifiers = $this->defaultModifiers;
            } else {
                $this->globalModifiers = $modifiers;
            }
        }
        
        
        public function setUpPlayer(Player $player, bool $first = false) : void
        {
            $playerName = $player->getName();
            $playerArray = [
                'attributes' => $this->defaultStats,
                'skills' => [],
                'level' => 0,
                ];
            $this->players[$playerName] = $playerArray;
            $player->setXpLevel(1);
            if($first) {
                $this->upgradeStatsForm($player, 1);
            }
            $this->saveVars();
        }
        
        public function upgradeStatsForm(Player $player, int $spleft)
        {
            if($spleft <= 0) {
                $this->statsForm($player);
                return;
            }
            $playerName = $player->getName();
            $messages = $this->parseMessages($player, $spleft);
            
            $form = new SimpleForm(function(Player $player, $data) use ($playerName, $spleft) {
                switch($data) {
                    case "Strength":
                        $this->increasePlayerAttribute($playerName, 'STR');
                        $this->calcDamage($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Vitality":
                        $this->increasePlayerAttribute($playerName, 'VIT');
                        $this->calcVitality($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Defense":
                        $this->increasePlayerAttribute($playerName, 'DEF');
                        $this->calcDefense($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Dexterity":
                        $this->increasePlayerAttribute($playerName, 'DEX');
                        $this->calcDexterity($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
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
            $player->sendForm($form);
            $this->checkForSkills($player);
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
        
        public function statsForm(Player $player)
        {
            $messages = $this->parseMessages($player, 0, true);
            
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
            $player->sendForm($form);
            
        }
        
        public function checkForSkills(Player $player)
        {
            $playerName = $player->getName();
            
            foreach($this->getSkillsManager()->getSkills() as $skill) {
                
                if($skill->skillUnlock($playerName) && $this->playerHasSkill($skill->getName(), $playerName) == false) {
                    $skill->setPlayerSkill($playerName);
                    $this->descriptionSkillForm($player, $skill->getDescription());
                }
                if($skill->skillUpgrade($playerName)){
                    $skill->setSkillLevel($skill->getSkillLevel() + 1);
                }
                
            }
        }
        
        
        public function parseMessages(Player $player, int $spleft, bool $stats = false) : array
        {
            if($stats == true) {
                $messages = $this->main->globalMessages['StatsForm'];
            } else {
                $messages = $this->main->globalMessages['UpgradeForm'];
            }
            
            $playerName = $player->getName();
            
            $messages['spleft'] = $spleft;
            
            $stats = [
                "STR" => $this->getPlayerAttribute($playerName, 'STR'),
                "VIT" => $this->getPlayerAttribute($playerName, 'VIT'),
                "DEF" => $this->getPlayerAttribute($playerName, 'DEF'),
                "DEX" => $this->getPlayerAttribute($playerName, 'DEX'),
                "SPLEFT" => $spleft,
                "PLAYER" => $playerName,
                ];
            $joined = array_merge($stats, $this->main->consts);
            
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
        
        public function setPlayerLevel(string $playerName, int $value) : void
        {
            $this->players[$playerName]['level'] = $value;
        }
        
        public function getPlayerLevel(string $playerName) : int
        {
            return $this->players[$playerName]['level'];
        }
    
        public function setPlayerAttribute(string $playerName, string $attribute, $value) : void
        {
            $this->players[$playerName]['attributes'][$attribute] = $value;
        }
        public function getPlayerAttribute(string $playerName, string $attribute) : int
        {
            return $this->players[$playerName]['attributes'][$attribute];
        }
        
        public function increasePlayerAttribute(string $playerName, string $attribute, int $incrementWith = 1)
        {
            $this->players[$playerName]['attributes'][$attribute] += $incrementWith;
        }
        
        public function applyDamageBonus(EntityDamageByEntityEvent $event) : void
        {
            $damager = $event->getDamager();
            if($damager instanceof Player){
                $baseDamage = $event->getBaseDamage();
                $event->setBaseDamage($baseDamage + $this->getPlayerDamage($damager->getName()));
            }
        }
        public function calcDamage(string $playerName)
        {
            $str = $this->getPlayerAttribute($playerName, 'STR');
            $strModifier = $this->globalModifiers['STR'];
            $this->players[$playerName]['damage'] =  $str * $strModifier;
        }
        
        public function getPlayerDamage(string $playerName) : float
        {
            return $this->players[$playerName]['damage'];
        }
        
        public function applyVitalityBonus(Player $player)
        {
            $playerName = $player->getName();
            $player->setMaxHealth(20 + $this->getPlayerVitality($playerName));
            $player->setHealth(20 + $this->getPlayerVitality($playerName));
        }
        public function calcVitality(string $playerName) : void
        {
            $vitality = $this->getPlayerAttribute($playerName, 'VIT');
            $vitModifier = $this->globalModifiers['VIT'];
            $this->players[$playerName]['vitalityBonus'] = 20 + ($vitality * $vitModifier);
        }
        public function getPlayerVitality(string $playerName) : ?float
        {
            return $this->players[$playerName]['vitalityBonus'];
        }
        public function applyDefenseBonus(EntityDamageByEntityEvent $event) : void
        {
            $receiver = $event->getEntity();
            if($receiver instanceof Player){
                $receiver->setAbsorption($receiver->getAbsorption() + $this->getPlayerDefense($receiver->getName()));
            }
        }
        public function calcDefense(string $playerName)
        {
            $def = $this->getPlayerAttribute($playerName, 'DEF');
            $defModifier = $this->globalModifiers['DEF'];
            $this->players[$playerName]['defenseBonus'] = $def * $defModifier;
        }
        
        public function getPlayerDefense(string $playerName) : ?float
        {
            return $this->players[$playerName]['defenseBonus'];
        }
        
        public function applyDexterityBonus(Player $player)
        {
            $playerName = $player->getName();
            $dex = $this->getPlayerDexterity($playerName);
            $movement = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
            $movement->setValue($movement->getValue()  * $dex);
        }
        
        public function calcDexterity(string $playerName) : void
        {
            $dex = $this->getPlayerAttribute($playerName, 'DEX');
            $dexModifier = $this->globalModifiers['DEX'];
            $this->players[$playerName]['dexterity'] = 1 + ($dex * $dexModifier);
        }
        public function getPlayerDexterity(string $playerName) : ?float
        {
            return $this->players[$playerName]['dexterity'];
        }
        
        
        public function wipePlayer(string $playerName) : void
        {
            $this->players[$playerName] = [];
        }
        
        public function playerExists(Player $player) : bool
        {
            return array_key_exists($player->getName(), $this->players);
        }
    
        public function playerHasSkill(string $skillName, string $playerName) : bool
        {
            return in_array($skillName, $this->players[$playerName]['skills']);
        }
        public function getPlayerSkills(string $playerName) : array
        {
            return $this->players[$playerName]['skills'];
        }
        public function getPlayers() : array
        {
            return $this->players;
        }
        public function saveVars() : void
        {
            $conf = $this->config;
            $conf->setNested('players', $this->players);
            $conf->save();
        }
        public function getMain() : Main
        {
            return $this->main;
        }
        public function getSkillsManager() : SkillsManager
        {
            return $this->skillsManager;
        }
        public static function getInstance() : RPGLike
        {
            return self::$instance;
        }
    }