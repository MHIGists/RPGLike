<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;
    
    use pocketmine\entity\Effect;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    class Coinflip extends BaseSkill
    {
        public function __construct(string $owner, string $namespace)
        {
            parent::__construct($owner, $namespace);
            $this->setName('Coinflip');
            $this->setType('passive');
            $this->setAttribute(['STR']);
            $this->setBaseUnlock(10);
            $this->setCooldownTime(0);
            $description = [
                'title' => 'You\'ve unlocked the Coinflip skill!',
                'content' => '"Coinflip" increases your health by 15%',
                'exitButton' => 'Great!'
            ];
            $this->setDescription($description);
            $this->setMaxEntInRange(1);
            $this->setRange(0);
            $this->setEffect(Effect::HEALTH_BOOST);
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