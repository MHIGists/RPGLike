<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;

    use TheClimbing\RPGLike\Players\RPGPlayer;

    class Fortress extends BaseSkill
    {
        
        public function __construct(RPGPlayer $owner, string $namespace)
        {
            $this->setName('Fortress');
            $this->setType('passive');
            $this->setCooldownTime(0);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
            $this->setDefense($owner);
            parent::__construct($owner, $namespace);
        }
        public function setDefense(RPGPlayer $player) : void
        {
            $player->setAbsorption($player->getAbsorption() * 1.2);
        }
    }