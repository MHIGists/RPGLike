<?php

declare(strict_types = 1);

namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

use TheClimbing\RPGLike\Players\RPGPlayer;


class Explosion extends BaseSkill
{
    protected $owner;
    public function __construct(RPGPlayer $owner)
    {
        $this->owner = $owner;
        $this->setCooldownTime(10000);
        parent::__construct($owner, 'Explosion');
    }
    public function damageEvent(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        if ($damager instanceof Player)
        {
            if ($damager->getInventory()->getItemInHand()->getId() == 433)
            {
                if ($this->isOnCooldown())
                {
                    $damager->sendPopup('Skill on cooldown: ' . $this->getRemainingCooldown());
                }
                $pos = $event->getEntity()->getPosition();
                switch ($this->getSkillLevel()){
                    case 0:
                        $explosion = new \pocketmine\level\Explosion($pos, 4);
                        break;
                    case 1:
                        $explosion = new \pocketmine\level\Explosion($pos, 5);
                        break;
                    case 2:
                        $explosion = new \pocketmine\level\Explosion($pos, 6);
                        break;
                    default:
                        $explosion = new \pocketmine\level\Explosion($pos, 4);
                }

                $explosion->explodeB();
                $this->setOnCooldown();
            }
        }
    }
}