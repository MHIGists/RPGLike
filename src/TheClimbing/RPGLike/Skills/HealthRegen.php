<?php


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityRegainHealthEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class HealthRegen extends BaseSkill
{
    public function __construct(RPGPlayer $owner)
    {
        $this->setType('active');
        $this->setCooldownTime(30);
        $this->setMaxEntInRange(1);
        $this->setRange(0);
        parent::__construct($owner, 'HealthRegen');
    }

    public function healthRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        if ($event->getRegainReason() == EntityRegainHealthEvent::CAUSE_SATURATION) {
            $event->setAmount($event->getAmount() + $player->getHealthRegenBonus());
        }
    }
}