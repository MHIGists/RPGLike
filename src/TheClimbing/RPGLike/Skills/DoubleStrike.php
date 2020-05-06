<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;
    
    use pocketmine\entity\Effect;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    class DoubleStrike extends BaseSkill
    {
        public function __construct(string $owner, string $namespace)
        {
            parent::__construct($owner, $namespace);
            $this->setName('DoubleStrike');
            $this->setType('passive');
            $this->setCooldownTime(0);
            $this->setDescription();
            $this->setMaxEntInRange(1);
            $this->setRange(0);
            $this->setEffect(Effect::HEALTH_BOOST);
            $this->setUpgrades([10, 20, 30]);
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