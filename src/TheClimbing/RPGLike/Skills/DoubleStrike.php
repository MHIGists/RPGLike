<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;
use function rand;

class DoubleStrike extends BaseSkill
{
    public array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['DoubleStrike']['levels'];
        $this->setType('active');
        $this->setMaxEntInRange(1);
        $this->setRange(0);
        parent::__construct($owner, 'DoubleStrike', $this->config);
    }

    public function setPlayerAttackCD(EntityDamageByEntityEvent $event)
    {
        if (rand(0, 99) < $this->config[$this->getSkillLevel()]['chance']) {
            $event->setAttackCooldown(0);
        }
    }
}