<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\Players\RPGPlayer;

class Fortress extends BaseSkill implements PassiveSkill
{
    public function __construct(RPGPlayer $owner)
    {
        parent::__construct($owner, 'Fortress');
    }

    public function setDefense(RPGPlayer $player): void
    {
        $player->setAbsorption($player->getAbsorption() * 1.2);
    }
}