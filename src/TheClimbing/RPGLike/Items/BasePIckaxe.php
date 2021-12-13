<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\Pickaxe;
use pocketmine\item\ToolTier;

class BasePIckaxe extends Pickaxe
{
    use BaseItem;
    public function __construct(ItemIdentifier $identifier, string $name, ToolTier $tier, array $bonus)
    {
        parent::__construct($identifier, $name, $tier);
        $this->init('',$bonus);
    }
}