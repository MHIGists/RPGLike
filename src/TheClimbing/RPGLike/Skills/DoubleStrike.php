<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class DoubleStrike extends BaseSkill
{
    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'DoubleStrike');
    }

    public function setPlayerAttackCD(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer){
            if (mt_rand(0, 99) < $this->skillConfig[$this->getSkillLevel()]['chance']) {
                $event->setAttackCooldown(0);
                $this->transmitProcMessage($player);
            }
        }
    }
}