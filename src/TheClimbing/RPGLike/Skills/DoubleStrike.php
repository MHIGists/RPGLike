<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 7:34 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function rand;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    use TheClimbing\RPGLike\RPGLike;

    class DoubleStrike extends BaseSkill
    {
        public function __construct(RPGLike $rpg)
        {
            parent::__construct('Double Striker', 'passive', 'Has a 10% chance to reset your basic attack.', 0, 0, 'DEX', $rpg);
        }
        public function setPlayerAttackCD(EntityDamageByEntityEvent $event)
        {
            if(rand(0,99) < 10)
            {
                $event->setAttackCooldown(0);
            }
        }
    }