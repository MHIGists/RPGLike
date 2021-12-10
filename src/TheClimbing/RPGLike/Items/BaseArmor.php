<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class BaseArmor extends Armor
{
    use BaseItem;
    public function __construct(int $item_id, string $name,int $defense_points, int $max_durability, int $armor_type, array $bonus)
    {
        parent::__construct(new ItemIdentifier($item_id, 0), $name, new ArmorTypeInfo($defense_points,$max_durability,$armor_type));
        $this->init($name, $bonus);
    }

}