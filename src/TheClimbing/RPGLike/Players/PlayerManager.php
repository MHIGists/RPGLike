<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Players;
    
    
    use pocketmine\Player;
    
    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Skills\BaseSkill;
    use TheClimbing\RPGLike\Skills\SkillsManager;

    class PlayerManager
    {
        private static $rpg;
        private static $players = [];
        private static $skills = [];
        private static $instance = null;
        
        public function __construct(RPGLike $rpg)
        {
            self::$rpg = $rpg;
            self::$instance = $this;
            self::$skills = $rpg->getConfig()->getNested('Skills');
        }
        public static function makePlayer(string $playerName, array $modifiers)
        {
            $cachedPlayer = self::hasPlayed($playerName);
            if($cachedPlayer != false) {
                self::$players[$playerName] = new RPGPlayer($playerName, $modifiers);
                $attributes = $cachedPlayer['attributes'];
                $player = self::getPlayer($playerName);
                $player->setDEF($attributes['DEF']);
                $player->setDEX($attributes['DEX']);
                $player->setSTR($attributes['STR']);
                $player->setVIT($attributes['VIT']);
    
                $player->setLevel($cachedPlayer['level']);
                
                $player->calcDEXBonus();
                $player->calcDEFBonus();
                $player->calcVITBonus();
                $player->calcSTRBonus();
                
                if(!empty($cachedPlayer['skills'])){
                    foreach($cachedPlayer['skills'] as $skill) {
                        $player->unlockSkill(SkillsManager::getSkillNamespace($skill), $skill, false);
                    }
                }
            }
            if($cachedPlayer == false){
                self::$players[$playerName] = new RPGPlayer($playerName, $modifiers);
            }
        }
        public static function getSkills() : array
        {
            return self::$skills;
        }
        public static function getCachedPlayer()
        {
            return self::$rpg->getConfig()->getNested('players');
        }
    
        /**
         * @param string $playerName
         *
         * @return false|array
         */
        public static function hasPlayed(string $playerName)
        {
            $players = self::getCachedPlayer();
            if($players != null){
                if(array_key_exists($playerName, $players)){
                    return $players[$playerName];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    
        /**
         * @param string $playerName
         *
         * @return false|RPGPlayer
         */
        public static function getPlayer(string $playerName)
        {
            if(array_key_exists($playerName, self::$players)){
                return self::$players[$playerName];
            }else {
                return false;
            }
        }
        
        public static function getServerPlayer(string $playerName) : Player
        {
            return self::$rpg->getServer()->getPlayer($playerName);
        }
        
        public static function getPlayers() : array
        {
            return self::$players;
        }
        
        public static function removePlayer(string $playerName)
        {
            
            RPGLike::getInstance()->savePlayerVariables($playerName);
            unset(self::$players[$playerName]);
        }
        
        public static function getInstance() : PlayerManager
        {
            return self::$instance;
        }
    }