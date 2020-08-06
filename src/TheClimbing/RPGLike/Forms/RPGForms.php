<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Forms;
    
    use pocketmine\Player;

    use pocketmine\utils\TextFormat;

    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Players\RPGPlayer;
    use TheClimbing\RPGLike\Skills\SkillsManager;
    use TheClimbing\RPGLike\Utils;

    use jojoe77777\FormAPI\SimpleForm;

    class RPGForms
    {

        public static function upgradeStatsForm(RPGPlayer $player, int $spleft)
        {
            if ($player->getSPleft() > 0)
            {
                $spleft = $player->getSPleft();
                $player->setSPleft(0);
            }
            if($spleft <= 0) {
                self::statsForm($player);
                return;
            }
            $messages = self::parseMessages($player, 'UpgradeForm', $spleft );
        
            $form = new SimpleForm(function(Player $pl, $data) use ($player, $spleft) {
                switch($data) {
                    case "strength":
                        $player->setSTR($player->getSTR() + 1);
                        $spleft--;
                        break;
                    case "vitality":
                        $player->setVIT($player->getVIT() + 1);
                        $spleft--;
                        RPGLike::getInstance()->applyVitalityBonus($pl);
                        break;
                    case "defense":
                        $player->setDEF($player->getDEF() + 1);
                        $spleft--;
                        break;
                    case "dexterity":
                        $player->setDEX($player->getDEX() + 1);
                        $spleft--;
                        RPGLike::getInstance()->applyDexterityBonus($pl);
                        break;
                    case "exit":
                        if ($spleft > 0)
                        {
                            $player->setSPleft($spleft);
                        }
                        return;
                        default:
                        if ($spleft > 0)
                        {
                            $player->setSPleft($spleft);
                        }
                        return;
                }
                self::upgradeStatsForm($player, $spleft);

            });
        
            $form->setTitle($messages['title']);
            $form->setContent($messages['content']);
            foreach($messages['buttons'] as $key => $button) {
                $form->addButton($button, -1, '', $key);
            }
            $player->sendForm($form);
            $player->checkForSkills();
        }
    
        public static function skillHelpForm(RPGPlayer $player, string $skillName)
        {
            $skillDescription = Utils::parseArrayKeywords(RPGLike::getInstance()->consts, SkillsManager::getSkillDescription($skillName));
            $form = new SimpleForm(function(Player $pl, $data) use ($player) {
                switch($data) {
                    case 'Back':
                        self::skillsHelpForm($player);
                        break;
                }
            });
            $form->setTitle(RPGLike::getInstance()->getMessages()['Forms']['SkillInfo']['title']);
            $form->setContent($skillDescription['description'] . TextFormat::EOL . $skillDescription['unlocks']);
            $form->addButton('Back to menu', -1, '', 'Back');
            $player->sendForm($form);
        }
    
        public static function statsForm(RPGPlayer $player)
        {
            $messages = self::parseMessages(($player), 'StatsForm');
        
            $form = new SimpleForm(function(Player $player, $data) {});
            $form->setTitle($messages['title']);
            $form->setContent($messages['content']);
            foreach($messages['buttons'] as $key => $message) {
                $form->addButton($message, -1, '', $key);
            }
            $player->sendForm($form);
        
        }
        public static function menuForm(RPGPlayer $player)
        {
            $menuStrings = self::parseMessages($player, 'MenuForm');
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
            $form->setTitle($menuStrings['title']);
            foreach($menuStrings['buttons'] as $key => $buttonText) {
                $form->addButton($buttonText, -1, '', $key);
            }
            if(array_key_exists('content', $menuStrings)){
                $form->setContent($menuStrings['content']);
            }else{
                RPGLike::getInstance()->getLogger()->alert('Please check messages.yml Menu Form contents are empty');
            }
            $player->sendForm($form);
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
                    if ($data == 'back')
                    {
                        self::menuForm($player);
                    }
            });
            $form->setTitle("All available skills");
            foreach ($skills as $skill) {
                $form->addButton($skill, -1, '', $skill);
            }
            $form->addButton("Back to main menu", -1, '', 'Back');
            $player->sendForm($form);
        }
        public static function parseMessages(RPGPlayer $player ,string $type, int $spleft = 0) : array
        {
            $messages = RPGLike::getInstance()->getMessages()['Forms'][$type];
            $stats = $player->getAttributes();
            $stats['PLAYER'] = $player->getName();
            $stats['SPLEFT'] = $spleft;

            $joined = array_merge($stats, RPGLike::getInstance()->consts);
            return Utils::parseArrayKeywords($joined, $messages);
        }
    }