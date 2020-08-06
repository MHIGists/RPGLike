<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    


    use function rand;

    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Players\RPGPlayer;

    class DoubleStrike extends BaseSkill
    {
        public function __construct(RPGPlayer $owner, string $namespace, bool $dummy = false)
        {
            parent::__construct($owner, $namespace, ["DEX" => 10], $dummy);
            $this->setName('DoubleStrike');
            $this->setType('passive');
            $this->setCooldownTime(0);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
        }
        public function setPlayerAttackCD(EntityDamageByEntityEvent $event)
        {
            $level = $this->getSkillLevel();
            switch($level){
                case 0:
                    if(rand(0,99) < 10)
                    {
                        $event->setAttackCooldown(0);
                    }
                    break;
                case 1:
                    if(rand(0,99) < 15)
                    {
                        $event->setAttackCooldown(0);
                    }
                    break;
                case 2:
                    if(rand(0,99) < 30)
                    {
                        $event->setAttackCooldown(0);
                    }
                    break;
            }
            
        }
    }