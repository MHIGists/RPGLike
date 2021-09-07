<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;

use TheClimbing\RPGLike\Players\RPGPlayer;


class Explosion extends BaseSkill
{
    protected RPGPlayer $owner;

    public function __construct(RPGPlayer $owner)
    {
        $this->owner = $owner;
        parent::__construct($owner, 'Explosion');
        $this->setCooldownTime(400);
    }

    public function damageEvent(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        if ($damager instanceof RPGPlayer) {
            if ($damager->getInventory()->getItemInHand()->getId() == 433) {
                if ($this->isOnCooldown()) {
                    $damager->sendMessage('Skill on cooldown: ' . $this->getRemainingCooldown('M:S') . ' left');
                    return;
                }
                $pos = $event->getEntity()->getPosition();
                $explosion = match ($this->getSkillLevel()) {
                    1 => new \pocketmine\level\Explosion($pos, 4),
                    2 => new \pocketmine\level\Explosion($pos, 3),
                    default => new \pocketmine\level\Explosion($pos, 5),
                };
                $explosion->explodeB();
                $this->setOnCooldown();
            }
        }
    }
}