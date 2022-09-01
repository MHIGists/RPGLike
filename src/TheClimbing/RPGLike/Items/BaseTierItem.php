<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use TheClimbing\RPGLike\Items\RPGLike;

class BaseTierItem extends TieredTool
{
    public string $item_tier;
    public array $custom_lore;
    public string $glow_colour;
    public array $bonus;
    public array $available_bonuses = [
        'damage',
        'health',
        'defense',
        'movement_speed',
        'mining_speed',
        'jump_power',
        'mana' //add mana for active skills??
    ];

    public function __construct(int $id, int $meta, string $name, ToolTier $tier, string $item_tier, array $bonus)
    {
        parent::__construct(new ItemIdentifier($id, $meta), $name, $tier);
        $this->item_tier = $item_tier;
        if (array_search($bonus[array_key_first($bonus)], $this->available_bonuses) != false) {
            $this->bonus = $bonus;
        } else {
            RPGLike::getInstance()->getLogger()->alert('All available bonuses are: damage, health, defense, movement_speed, mining_speed, jump_power, mana');
            $this->bonus = ['damage' => 1];
        }
        $this->setEnchantGlow();
    }

    public function setEnchantGlow()
    {
        $this->setNamedTag((new CompoundTag())->setTag(self::TAG_ENCH, new ListTag()));
    }

    public function getItemBonus(): array
    {
        return $this->bonus;
    }

    public function setCustomLore(string $tier)
    {
        $this->custom_lore = RPGLike::getInstance()->getTieredItems()[$tier][$this->getCustomName()];
        $this->setLore($this->custom_lore);
    }
}