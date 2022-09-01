<?php


namespace TheClimbing\RPGLike\Skills;


use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Tasks\HealTask;

class HealingAura extends BaseSkill implements AreaOfEffect, PassiveSkill
{
    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'HealingAura');
    }
    public function checkRange(): void
    {
        if (!$this->isOnCooldown()){
            if ($this->getRange() > 0) {
                $world = $this->owner->getWorld();
                $players = $this->getNearestEntities($this->owner->getPosition(), $this->getRange(), $world, $this->getMaximumEntitiesInRange());
                if (!empty($players)) {
                    foreach ($players as $key => $player) {
                        if ($key == 0) {
                            continue;
                        }
                        $this->passiveEffect($player);
                    }
                }
            }
        }
    }
    public function passiveEffect(mixed $mixed)
    {
        $this->setOnCooldown();
        RPGLike::getInstance()->getScheduler()->scheduleDelayedTask(new HealTask($this, $mixed), 5);
    }
}