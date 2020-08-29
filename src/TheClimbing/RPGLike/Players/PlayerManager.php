<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Players;
    
    
    use pocketmine\Player;
    
    use TheClimbing\RPGLike\RPGLike;

    class PlayerManager
    {
        /* @var PlayerManager */
        private static $main;
        
        public function __construct(RPGLike $rpg)
        {
            self::$main = $rpg;
        }
        public static function makePlayer(Player $player)
        {
            $cachedPlayer = self::hasPlayed($player->getName());

            if($cachedPlayer != false) {
                $attributes = $cachedPlayer['attributes'];
                $player->setDEF($attributes['DEF']);
                $player->setDEX($attributes['DEX']);
                $player->setSTR($attributes['STR']);
                $player->setVIT($attributes['VIT']);
    
                $player->setXPLevel($cachedPlayer['level']);
                
                $player->calcDEXBonus();
                $player->calcDEFBonus();
                $player->calcVITBonus();
                $player->calcSTRBonus();

                $player->setSPleft($cachedPlayer['spleft']);
                if(!empty($cachedPlayer['skills'])){
                    foreach($cachedPlayer['skills'] as $skill) {
                        $player->getSkill($skill)->unlock();
                    }
                }
            }
        }


        public static function getCachedPlayers()
        {
            return self::$main->getConfig()->getNested('Players');
        }
    
        /**
         * @param string $playerName
         *
         * @return false|array
         */
        public static function hasPlayed(string $playerName)
        {
            $players = self::getCachedPlayers();
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

    }