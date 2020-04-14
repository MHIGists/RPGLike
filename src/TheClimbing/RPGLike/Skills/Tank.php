<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;
    use TheClimbing\RPGLike\RPGLike;

    class Tank extends BaseSkill
    {
        public function __construct(RPGLike $rpg)
        {
            $name = 'Tank';
            $type = 'passive';
            $description = [
                'title' => 'You\'ve unlocked the Tank skill!',
                'content' => '"Tank" increases your health by 15%',
                'exitButton' => 'Sweet!'
            ];
            $cooldown = 0;
            $range = 0;
            $attribute = 'DEF';
            parent::__construct($rpg,$name, $type, $description, $cooldown, $range, $attribute);
          
        }
        public function setPlayerHealth(Player $player)
        {
            $health = $player->getMaxHealth();
            $player->setMaxHealth($health * 1.15);
        }
    }