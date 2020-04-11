<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 6:59 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;
    
    use pocketmine\entity\Effect;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    use TheClimbing\RPGLike\RPGLike;

    class Coinflip extends BaseSkill
    {
        public function __construct(RPGLike $rpg)
        {
            parent::__construct($rpg,'Coinflip', 'passive', 'Has a 10% chance to to increase damage dealt by 50%', 0, 0, 'STR', Effect::JUMP_BOOST);
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