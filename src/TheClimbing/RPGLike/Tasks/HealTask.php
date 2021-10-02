<?php

namespace TheClimbing\RPGLike\Tasks;

use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\Skills\BaseSkill;

class HealTask extends \pocketmine\scheduler\Task
{
    private BaseSkill $source;
    private RPGPlayer $target;
    public function __construct(BaseSkill $source, RPGPlayer $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    public function onRun(int $currentTick)
    {
        $this->source->removeCooldown();
        $target = $this->target;
        $this->source->setPlayerEffect(function () use ($target) {
            $target->setHealth($target->getHealth() + 1);
        });
    }
}