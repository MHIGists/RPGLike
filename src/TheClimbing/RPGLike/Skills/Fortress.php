<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;

    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Players\RPGPlayer;

    class Fortress extends BaseSkill
    {
        
        public function __construct(RPGPlayer $owner, string $namespace, bool $dummy = false)
        {
            parent::__construct($owner, $namespace, ['DEF' => 10], $dummy);
            $this->setName('Fortress');
            $this->setType('passive');
            $this->setCooldownTime(0);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
            if (!$dummy)
            {
                $this->setDefense($owner);
            }
        }
        public function setDefense(RPGPlayer $player) : void
        {
            $player->setAbsorption($player->getAbsorption() * 1.2);
        }
    }