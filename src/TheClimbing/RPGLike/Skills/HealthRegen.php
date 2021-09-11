<?php


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityRegainHealthEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class HealthRegen extends BaseSkill
{
    public array $config;
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['HealthRegen']['levels'];

        $this->setType('active');
        $this->setCooldownTime(30);
        $this->setRange(0);
        parent::__construct($owner, 'HealthRegen', $this->config);
    }

    public function healthRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        if ($event->getRegainReason() == EntityRegainHealthEvent::CAUSE_SATURATION) {
            $event->setAmount($event->getAmount() + $player->getHealthRegenBonus());
        }
    }
}