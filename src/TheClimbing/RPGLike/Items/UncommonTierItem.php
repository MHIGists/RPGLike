<?php

namespace TheClimbing\RPGLike\Items;

class UncommonTierItem extends BaseTierItem
{
    public function __construct(int $id, int $meta, string $name, int $tier,array $bonus)
    {
        parent::__construct($id, $meta, $name,  $tier,'uncommon', $bonus);
    }
}