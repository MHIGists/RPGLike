<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Forms;
    
    use pocketmine\Player;

    use pocketmine\utils\TextFormat;
    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Players\RPGPlayer;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Skills\SkillsManager;
    use TheClimbing\RPGLike\Utils;

    use jojoe77777\FormAPI\SimpleForm;


    class RPGForms
    {
        //I just hate the whole FORM API so have fun reading this, I didn't have fun writing it.

        public static function upgradeStatsForm(RPGPlayer $player, int $spleft)
        {
            if ($player->getSPleft() > 0)
            {
                $spleft += $player->getSPleft();
                $player->setSPleft(0);
            }
            if($spleft <= 0) {
                self::statsForm($player);
                return;
            }
            $messages = self::parseMessages($player->getName(), 'UpgradeForm', $spleft );
        
            $form = new SimpleForm(function(Player $pl, $data) use ($player, $spleft) {
                switch($data) {
                    case "Strength":
                        $player->setSTR($player->getSTR() + 1);
                        $spleft--;
                        break;
                    case "Vitality":
                        $player->setVIT($player->getVIT() + 1);
                        $spleft--;
                        RPGLike::getInstance()->applyVitalityBonus($pl);
                        break;
                    case "Defense":
                        $player->setDEF($player->getDEF() + 1);
                        $spleft--;
                        break;
                    case "Dexterity":
                        $player->setDEX($player->getDEX() + 1);
                        $spleft--;
                        RPGLike::getInstance()->applyDexterityBonus($pl);
                        break;
                    case "Exit":
                        if ($spleft > 0)
                        {
                            $player->setSPleft($spleft);
                        }
                        break;
                    default:
                        if ($spleft > 0)
                        {
                            $player->setSPleft($spleft);
                        }
                }
                self::upgradeStatsForm($player, $spleft);

            });
        
            $form->setTitle($messages['Title']);
            $form->setContent($messages['Content']);
            foreach($messages['Buttons'] as $key => $button) {
                $form->addButton($button, -1, '', $key);
            }
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
            $player->checkForSkills();
        }
    
        public static function skillHelpForm(RPGPlayer $player, string $skillName)
        {
            $messages = Utils::parseArrayKeywords(RPGLike::getInstance()->consts, SkillsManager::getSkillDescription($skillName));
            $form = new SimpleForm(function(Player $pl, $data) use ($player) {
                switch($data) {
                    case 'Back':
                        self::skillsHelpForm($player);
                        break;
                }
            });
            $form->setTitle(RPGLike::$messages['Forms']['SkillInfo']['Title']);
            $form->setContent($messages['Description'] . TextFormat::EOL . $messages['Unlocks']);
            $form->addButton('Back to menu', -1, '', 'Back');
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
        }
    
        public static function statsForm(RPGPlayer $player)
        {
            $messages = self::parseMessages($player->getName(), 'StatsForm');
        
            $form = new SimpleForm(function(Player $player, $data) {});
            $form->setTitle($messages['Title']);
            $form->setContent($messages['Content']);
            foreach($messages['Buttons'] as $key => $message) {
                $form->addButton($message, -1, '', $key);
            }
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
        
        }
        public static function menuForm(RPGPlayer $player)
        {
            $messages = self::parseMessages($player->getName(), 'MenuForm');
            $form = new SimpleForm(function(Player $pl, $data) use ($player) {
                switch($data){
                    case "skills":
                        self::skillsHelpForm($player);
                        break;
                    case "stats":
                        self::statsForm($player);
                        break;
                    case "upgrade":
                        self::upgradeStatsForm($player, 0);
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
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
        }
        public static function skillsHelpForm(RPGPlayer $player)
        {
            $skills = SkillsManager::getAvailableSkills();
            $form = new SimpleForm(function(Player $pl, $data) use ($skills, $player)
            {
                    foreach ($skills as $skill) {
                        if ($skill == $data){
                            self::skillHelpForm($player, $skill);
                        }
                    }
                    if ($data == 'Back')
                    {
                        self::menuForm($player);
                    }
            });
            $form->setTitle("All available skills");
            foreach ($skills as $skill) {
                $form->addButton($skill, -1, '', $skill);
            }
            $form->addButton("Back to main menu", -1, '', 'Back');
            PlayerManager::getServerPlayer($player->getName())->sendForm($form);
        }
        public static function parseMessages(string $playerName ,string $type, int $spleft = 0) : array
        {
            $messages = RPGLike::$messages['Forms'][$type];
            $stats = PlayerManager::getPlayer($playerName)->getAttributes();
            $stats['PLAYER'] = $playerName;
            $stats['SPLEFT'] = $spleft;

            $joined = array_merge($stats, RPGLike::getInstance()->consts);
            return Utils::parseArrayKeywords($joined, $messages);
        }
    }