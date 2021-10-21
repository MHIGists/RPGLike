<?php

namespace TheClimbing\RPGLike\Items;

class UncommonTierItem extends BaseTierItem
{
    public function __construct(array $bonus)
    {
        parent::__construct('uncommon', $bonus);
    }
}