<?php

declare(strict_types = 1);

namespace TheClimbing\RPGLike\Skills;


class Explosion extends BaseSkill
{
    public function __construct()
    {
        parent::__construct();
        $this->setName('Explosion');
        $this->setAttribute([
            'STR' => 20,
            'DEX' => 10
            ]);
    }
}