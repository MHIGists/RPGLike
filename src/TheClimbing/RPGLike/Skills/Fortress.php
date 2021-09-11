<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\Players\RPGPlayer;

class Fortress extends BaseSkill
{
    public array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['Fortress']['levels'];
        $this->setType('passive');
        $this->setCooldownTime(0);
        $this->setRange(0);
        parent::__construct($owner, 'Fortress', $this->config);
    }

    public function setDefense(RPGPlayer $player): void
    {
        $player->setAbsorption($player->getAbsorption() * 1.2);
    }
}