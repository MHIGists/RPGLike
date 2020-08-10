<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    

    use function rand;

    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use TheClimbing\RPGLike\Players\RPGPlayer;
    
    class Coinflip extends BaseSkill
    {
        public function __construct(RPGPlayer $owner)
        {
            parent::__construct($owner, "Coinflip");
            $this->setType('passive');
            $this->setCooldownTime(0);

        }
        public function setCritChance(EntityDamageByEntityEvent $event)
        {
            $damage = $event->getBaseDamage();
            if(rand(0, 99) < 10)
            {
                $event->setBaseDamage($damage * 1.5);
            }
        }
    }