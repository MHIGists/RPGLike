<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\TieredTool;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\CompoundTag;

use pocketmine\utils\TextFormat;

class UncommonTierSword extends Sword{
    use BaseTieredItem;
    public function __construct(array $uncommon_items)
    {
        parent::__construct(Item::IRON_SWORD, 0, 'Iron Sword', TieredTool::TIER_IRON);
        // This gives us the glow
        $this->setEnchantGlow();
        // Custom Lore example
        $this->setLore([
            TextFormat::AQUA . 'Juggernaut Set 1/5:',
            TextFormat::GRAY . '2/5 Damage +2',
            TextFormat::GRAY . '3/5 Health +2',
            TextFormat::GRAY . '4/5 Damage +2',
            TextFormat::GRAY . '5/5 Damage +5',
            TextFormat::AQUA . 'Effects:',
            TextFormat::AQUA . 'Health +1',
        ]);
        $this->setCustomName(TextFormat::GREEN . 'Uncommon Sword');

    }
}