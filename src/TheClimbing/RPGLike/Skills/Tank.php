<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\Players\RPGPlayer;

class Tank extends BaseSkill implements PassiveSkill
{

    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'Tank');
    }

    public function setPlayerHealth(RPGPlayer $player)
    {
        $health = $player->getMaxHealth();
        $player->setMaxHealth((int)($health * 1.15));
    }
}