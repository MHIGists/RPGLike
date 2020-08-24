<?php


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\Player;
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
    public function healtRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof Player)
        {
            $event->setAmount($event->getAmount() + $this);
        }
    }
}