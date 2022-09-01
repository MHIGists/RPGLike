<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Skills;


use pocketmine\entity\Entity;
use TheClimbing\RPGLike\Players\RPGPlayer;


class Explosion extends BaseSkill implements ActiveSkill
{
    protected RPGPlayer $owner;
    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'Explosion');
    }

    public function damageEvent(Entity $damager, Entity $hit_entity)
    {
        if ($damager instanceof RPGPlayer) {
                if ($this->isOnCooldown()) {
                    $damager->sendMessage('Skill on cooldown: ' . $this->getRemainingCooldown('M:S') . ' left');
                    return;
                }
                $pos = $hit_entity->getPosition();
                $explosion = new \pocketmine\world\Explosion($pos, 2 + $this->getSkillLevel());
                $explosion->explodeB();
                $this->setOnCooldown();

        }
    }
}