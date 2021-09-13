<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class DoubleStrike extends BaseSkill
{
    public array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['DoubleStrike']['levels'];
        $this->setType('active');
        $this->setRange(0);
        parent::__construct($owner, 'DoubleStrike', $this->config);
    }

    public function setPlayerAttackCD(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer){
            if (mt_rand(0, 99) < $this->config[$this->getSkillLevel()]['chance']) {
                $event->setAttackCooldown(0);
                $this->transmitProcMessage($player);
            }
        }
    }
}