<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Forms;
    
    use pocketmine\Player;
    use pocketmine\utils\Config;
    
    use jojoe77777\FormAPI;
    
    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Players\RPGPlayer;
    use TheClimbing\RPGLike\Players\PlayerManager;

    class RPGForms
    {
        //I just hate the whole FORM API so have fun reading this, I don't have fun writing it. This piece hurts my eyes
        private static $messages;
        
        public function __construct(RPGLike $rpg)
        {
            $messages = (new Config($rpg->getDataFolder() . 'messages.yml', Config::YAML))->getAll();
            self::$messages = $messages;
        }
        public static function upgradeStatsForm(RPGPlayer $player, int $spleft)
        {
            if($spleft <= 0) {
                self::statsForm($player);
                return;
            }
            $messages = self::parseMessages($player->getName(), 'UpgradeForm', $spleft );
        
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
                        RPGLike::getInstance()->applyVitalityBonus($pl);
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
                        RPGLike::getInstance()->applyDexterityBonus($pl);
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
    
        public static function descriptionSkillForm(Player $player, array $skillDescription)
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
    
        public static function statsForm(RPGPlayer $player)
        {
            $messages = self::parseMessages($player->getName(), 'StatsForm');
        
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
        public static function RPGMenuForm(RPGPlayer $player)
        {
            $messages = self::parseMessages($player->getName(), 'RPGMenu');
            $form = new SimpleForm(function(Player $pl, $data) use ($player) {
                switch($data){
                    case "skills":
                        $this->skillsForm($player);
                        break;
                    case "stats":
                        $this->statsForm($player);
                        break;
                }
            });
            $form->setTitle($messages['Title']);
            foreach($messages['Buttons'] as $key => $buttonText) {
                $form->addButton($buttonText, -1, '', $key);
            }
            if(array_key_exists('Content', $messages)){
                $form->setContent($messages['Content']);
            }
        }
        //TODO finish this
        public static function skillsHelpForm(RPGPlayer $player)
        {
            $form = new SimpleForm(function(Player $pl, $data)
            {
               switch($data){
                   case "":
                   
               }
            });
        }
        public static function helpForm(RPGPlayer $player)
        {
        
        }
        public static function parseMessages(string $playerName ,string $formType, int $spleft = 0) : array
        {
           $messages = self::$messages[$formType];
            $stats = PlayerManager::getPlayer($playerName)->getAttributes();
            $stats['PLAYER'] = $playerName;
            $stats['SPLEFT'] = $spleft;
        
            $joined = array_merge($stats, RPGLike::getInstance()->consts);
        
            $messages['FormContent'] = self::parseKeywords($joined, $messages['FormContent']);
        
            return $messages;
        }
    
        public static function parseKeywords(array $keywords, string $subject) : string
        {
            $subject = str_replace(['{', '}'], [' ', ' '], $subject);
            foreach($keywords as $key => $value) {
                $subject = str_replace($key, $value, $subject);
            }
            return $subject;
        }
    }
