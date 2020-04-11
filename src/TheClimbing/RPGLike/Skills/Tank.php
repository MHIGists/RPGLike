<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 7:54 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    
    use pocketmine\Player;
    use TheClimbing\RPGLike\RPGLike;

    class Tank extends BaseSkill
    {
        public function __construct(RPGLike $rpg)
        {
            parent::__construct($rpg,'Tank', 'passive', 'Increases your health by additional 15%', 0, 0, 'DEF');
          
        }
        public function setPlayerHealth(Player $player)
        {
            $health = $player->getMaxHealth();
            $player->setMaxHealth($health * 1.15);
        }
    }