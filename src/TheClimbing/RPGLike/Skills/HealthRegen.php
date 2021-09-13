<?php


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityRegainHealthEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class HealthRegen extends BaseSkill implements AreaOfEffect,PassiveSkill
{
    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'HealthRegen');
    }

    public function healthRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer){
            if ($event->getRegainReason() == EntityRegainHealthEvent::CAUSE_SATURATION) {
                $event->setAmount($event->getAmount() + $player->getHealthRegenBonus());
            }
        }
    }
}