<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use TheClimbing\RPGLike\RPGLike;


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
        $ench = new ListTag([], NBT::TAG_End);
        $compound = $this->getNamedTag()->setTag(Item::TAG_ENCH, $ench);
        $this->setNamedTag($compound);
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