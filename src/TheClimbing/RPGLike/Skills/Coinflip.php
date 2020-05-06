<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;

    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    class Coinflip extends BaseSkill
    {
        public function __construct(string $owner, string $namespace, bool $dummy = false)
        {
            parent::__construct($owner, $namespace, ['STR' => 10], $dummy);
            $this->setName('Coinflip');
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