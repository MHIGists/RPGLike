<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;

    class Fortress extends BaseSkill
    {
        
        public function __construct()
        {
            parent::__construct();
            $this->setName('Tank');
            $this->setType('passive');
            $this->setAttribute(['DEF']);
            $this->setBaseUnlock(10);
            $this->setCooldownTime(0);
            $description = [
                'title' => 'You\'ve unlocked the Tank skill!',
                ''
            ];
            $this->setDescription($description);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
        }
        public function setDefense(Player $player) : void
        {
            $player->setAbsorption($player->getAbsorption() * 1.2);
        }
    }