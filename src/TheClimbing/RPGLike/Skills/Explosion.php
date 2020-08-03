<?php

declare(strict_types = 1);

namespace TheClimbing\RPGLike\Skills;


class Explosion extends BaseSkill
{
    public function __construct(string $owner, string $namespace)
    {
        parent::__construct($owner, $namespace, ['STR' => 20, 'DEX' => 10]);
        $this->setName('Explosion');
    }
}