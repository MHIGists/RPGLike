<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/10/2020
     * Time: 1:40 PM
     */
    
    namespace TheClimbing\RPGLike;
    
    use function array_merge;
    use function array_key_exists;
    use function str_replace;
    use function trim;
    
    use pocketmine\Player;
    
    use pocketmine\entity\Attribute;
    use pocketmine\entity\Entity;
    
    
    use TheClimbing\RPGLike\Skills\Coinflip;
    use TheClimbing\RPGLike\Skills\DoubleStrike;
    use TheClimbing\RPGLike\Skills\Fortress;
    use TheClimbing\RPGLike\Skills\Tank;
    
    use jojoe77777\FormAPI\SimpleForm;
    
    class RPGLike
    {
        public $main;
        public $players = [];
        public $skills = [];
        public $globalModifiers = [];
        
        public $defaultStats = ['STR' => 1, 'VIT' => 1, 'DEF' => 1, 'DEX' => 1,];
        public $defaultModifiers = ['STR' => 0.15, 'VIT' => 0.2, 'DEF' => 0.1, 'DEX' => 0.005,];
        private $config;
        
        public function __construct(Main $main)
        {
            $this->main = $main;
            $this->config = $main->getConfig();
            $this->setConf();
            $this->setModifiers();
            $this->getSkills();
            
        }
        
        public function setConf() : void
        {
            $conf = $this->config;
            $array = $conf->getNested('players');
            if($array !== null) {
                $this->players = $array;
            }
        }
        
        public function setModifiers() : void
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
            $playerArray = ['attributes' => $this->defaultStats, 'level' => 0,];
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
                        $this->setPlayerDamage($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Vitality":
                        $this->increasePlayerAttribute($playerName, 'VIT');
                        $this->setPlayerVitality($player);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Defense":
                        $this->increasePlayerAttribute($playerName, 'DEF');
                        $this->setPlayerDefense($playerName);
                        $spleft--;
                        $this->upgradeStatsForm($player, $spleft);
                        break;
                    case "Dexterity":
                        $this->increasePlayerAttribute($playerName, 'DEX');
                        $this->setPlayerDexterity($playerName);
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
        
        public function descriptionSkillForm(Player $player, string $skillDescription)
        {
            $form = new SimpleForm(function(Player $player, $data) {
                switch($data) {
                    case 'Exit':
                        break;
                }
            });
            $form->setTitle('You\'ve unlocked new skill!');
            $form->setContent($skillDescription);
            $form->addButton('Exit');
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
            foreach($this->skills as $skill) {
                
                if($skill->isSkillUnlocked($playerName) && $skill->playerHasSkill($playerName) == false) {
                    $skill->setPlayer($playerName);
                    $this->descriptionSkillForm($player, $skill->getDescription());
                }
                
            }
        }
        
        public function getSkills() : void
        {
            $this->skills['Tank'] = new Tank($this);
            $this->skills['Fortress'] = new Fortress($this);
            $this->skills['Coinflip'] = new Coinflip($this);
            $this->skills['DoubleStrike'] = new DoubleStrike($this);
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
            
            $stats = ["STR" => $this->getPlayerAttribute($playerName, 'STR'), "VIT" => $this->getPlayerAttribute($playerName, 'VIT'), "DEF" => $this->getPlayerAttribute($playerName, 'DEF'), "DEX" => $this->getPlayerAttribute($playerName, 'DEX'), "SPLEFT" => $spleft, "PLAYER" => $playerName,];
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
            $subject = trim($subject, ' ');
            return $subject;
        }
        
        
        public function setPlayerMovement(Player $player)
        {
            if($player instanceof Entity) {
                $playerName = $player->getName();
                $dex = $this->getPlayerDexterity($playerName);
                $movement = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
                $movement->setValue((($movement->getValue() / 1.3) + 0.0245) * $dex, false, true);
            }
        }
        
        public function setPlayerLevel(string $playerName, int $value) : void
        {
            $this->players[$playerName]['level'] = $value;
            $this->saveVars();
        }
        
        public function getPlayerLevel(string $playerName) : int
        {
            return $this->players[$playerName]['level'];
        }
        
        
        public function getPlayerAttribute(string $playerName, string $attribute) : int
        {
            return $this->players[$playerName]['attributes'][$attribute];
        }
        
        public function increasePlayerAttribute(string $playerName, string $property, int $incrementWith = 1)
        {
            $this->players[$playerName]['attributes'][$property] += $incrementWith;
            $this->saveVars();
        }
        
        public function setPlayerDamage(string $playerName)
        {
            $str = $this->getPlayerAttribute($playerName, 'STR');
            $strModifier = $this->globalModifiers['STR'];
            $this->players[$playerName]['damage'] = 1 + ($str * $strModifier);
            $this->saveVars();
        }
        
        public function getPlayerDamage(string $playerName) : float
        {
            return $this->players[$playerName]['damage'];
        }
        
        public function setPlayerVitality(Player $player)
        {
            $playerName = $player->getName();
            $vitality = $this->getPlayerAttribute($playerName, 'VIT');
            $vitModifier = $this->globalModifiers['VIT'];
            $player->setMaxHealth(20 + ($vitality * $vitModifier));
            $player->setHealth(20 + ($vitality * $vitModifier));
        }
        
        public function setPlayerDefense(string $playerName)
        {
            $def = $this->getPLayerAttribute($playerName, 'DEF');
            $defModifier = $this->globalModifiers['DEF'];
            $this->players[$playerName]['defense'] = 1 + ($def * $defModifier);
            $this->saveVars();
        }
        
        public function getPlayerDefense(string $playerName) : float
        {
            return $this->players[$playerName]['defense'];
        }
        
        public function setPlayerDexterity(string $playerName)
        {
            $dex = $this->getPlayerAttribute($playerName, 'DEX');
            $dexModifier = $this->globalModifiers['DEX'];
            $this->players[$playerName]['dexterity'] = 1 + ($dex * $dexModifier);
            $this->saveVars();
        }
        
        public function getPlayerDexterity(string $playerName) : float
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
        
        public function setPlayerAttribute(string $playerName, string $property, $value) : void
        {
            $this->players[$playerName]['attributes'][$property] = $value;
        }
        
        public function saveVars() : void
        {
            $conf = $this->config;
            $conf->setNested('players', $this->players);
            
            $skills = [];
            foreach($this->skills as $skill) {
                $skills[] = $skill->getPlayers();
            }
            $this->main->getConfig()->setNested('PlayerSkills', $skills);
            $conf->save();
        }
        
        
    }