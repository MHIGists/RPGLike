<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;
use function rand;



class Coinflip extends BaseSkill
{
    public array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['Coinflip']['levels'];
        parent::__construct($owner, "Coinflip", $this->config);
        $this->setType('active');
        $this->setCooldownTime(0);
    }

    public function setCritChance(EntityDamageByEntityEvent $event)
    {
        $damage = $event->getBaseDamage();
        if (rand(0, 99) < $this->config[$this->getSkillLevel()]) {
            $event->setBaseDamage($damage * 1.5);
        }
    }
}