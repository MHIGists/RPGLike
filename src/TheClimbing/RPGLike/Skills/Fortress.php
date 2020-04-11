<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 6:52 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    
    use pocketmine\Player;
    use TheClimbing\RPGLike\RPGLike;

    class Fortress extends BaseSkill
    {
        
        public function __construct(RPGLike $rpg)
        {
            parent::__construct($rpg,'Fortress', 'passive', 'Increases your absorption by 20%', 0, 0, 'VIT');
        }
        public function setDefense(Player $player) : void
        {
            $player->setAbsorption($player->getAbsorption() * 1.2);
        }
    }