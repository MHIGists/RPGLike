<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;
    
    use pocketmine\entity\Effect;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    class DoubleStrike extends BaseSkill
    {
        public function __construct()
        {
            parent::__construct();
            $this->setName('DoubleStrike');
            $this->setType('passive');
            $this->setAttribute('STR');
            $this->setBaseUnlock(10);
            $this->setCooldown(0);
            $description = [
                'title' => 'You\'ve got some Double Strikes... Sometimes!',
                'content' => 'Skill has 10/15/30 percent chance to reset your basic attack cooldown',
                'exitButton' => 'Exit'
            ];
            $this->setDescription($description);
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