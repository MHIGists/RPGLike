<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;

    class Fortress extends BaseSkill
    {
        
        public function __construct(string $owner, string $namespace)
        {
            parent::__construct($owner, $namespace);
            $this->setName('Tank');
            $this->setType('passive');
            $this->setBaseUnlock();
            $this->setCooldownTime(0);
            $this->setDescription($description);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
        }
        public function setDefense(Player $player) : void
        {
            $player->setAbsorption($player->getAbsorption() * 1.2);
        }
    }