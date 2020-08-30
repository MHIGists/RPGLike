<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\Players\RPGPlayer;

class Fortress extends BaseSkill
{

    public function __construct(RPGPlayer $owner)
    {
        $this->setType('passive');
        $this->setCooldownTime(0);
        $this->setMaxEntInRange(1);
        $this->setRange(0);
        parent::__construct($owner, 'Fortress');
    }

    public function setDefense(RPGPlayer $player): void
    {
        $player->setAbsorption($player->getAbsorption() * 1.2);
    }
}