<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\ToolTier;

class UncommonTierItem extends BaseTierItem
{
    public function __construct(int $id, int $meta, string $name,array $bonus)
    {
        parent::__construct($id, $meta, $name,  ToolTier::STONE(),'uncommon', $bonus);
    }
}