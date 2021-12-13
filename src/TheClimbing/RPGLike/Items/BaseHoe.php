<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Hoe;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

class BaseHoe extends Hoe
{
    use BaseItem;
    public function __construct(ItemIdentifier $identifier, string $name, ToolTier $tier, array $bonus)
    {
        parent::__construct($identifier, $name, $tier);
        $this->init('',$bonus);
    }
}