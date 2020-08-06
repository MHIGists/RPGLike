<?php

declare(strict_types = 1);

namespace TheClimbing\RPGLike\Skills;


use TheClimbing\RPGLike\Players\RPGPlayer;

class Explosion extends BaseSkill
{
    public function __construct(RPGPlayer $owner, string $namespace)
    {
        parent::__construct($owner, $namespace, ['STR' => 20, 'DEX' => 10]);
        $this->setName('Explosion');
    }
}