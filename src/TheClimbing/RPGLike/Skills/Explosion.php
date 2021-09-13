<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Skills;


use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use TheClimbing\RPGLike\Skills\BaseSkill;
use TheClimbing\RPGLike\Players\RPGPlayer;


class Explosion extends BaseSkill
{
    protected RPGPlayer $owner;
    public  array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->owner = $owner;
        $this->config = $owner->getConfig()->getNested('Skills')['Explosion']['levels'];
        parent::__construct($owner, 'Explosion', $this->config);
        $this->setCooldownTime(400);
    }

    public function damageEvent(Entity $damager, Entity $hit_entity)
    {
        if ($damager instanceof RPGPlayer) {
                if ($this->isOnCooldown()) {
                    $damager->sendMessage('Skill on cooldown: ' . $this->getRemainingCooldown('M:S') . ' left');
                    return;
                }
                $pos = $hit_entity->getPosition();
                $explosion = new \pocketmine\level\Explosion($pos, 2 + $this->getSkillLevel());
                $explosion->explodeB();
                $this->setOnCooldown();

        }
    }
}