<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Players\RPGPlayer;

    class Tank extends BaseSkill
    {
        public function __construct(RPGPlayer $owner, string $namespace, bool $dummy = false)
        {
            parent::__construct($owner, $namespace, ['VIT' => 10], $dummy);
            $this->setName('Tank');
            $this->setType('passive');
            $this->setCooldownTime(0);
            $this->setRange(0);
            if (!$dummy){
                $this->setPlayerHealth($owner);

            }
        }
        public function setPlayerHealth(RPGPlayer $player)
        {
            $health = $player->getMaxHealth();
            $player->setMaxHealth((int)($health * 1.15));
        }
    }