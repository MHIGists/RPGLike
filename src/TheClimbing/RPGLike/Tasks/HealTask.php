<?php

namespace TheClimbing\RPGLike\Tasks;

use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\Skills\BaseSkill;

class HealTask extends Task
{
    private BaseSkill $source;
    private RPGPlayer $target;
    public function __construct(BaseSkill $source, RPGPlayer $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    public function onRun() : void
    {
        $this->source->removeCooldown();
        $target = $this->target;
        $this->source->setPlayerEffect(function () use ($target) {
            $target->setHealth($target->getHealth() + 1);
        });
    }
}