<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\DiamondBoots;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;

class ItemFactory
{
    public static array $items = [];

    public static function addItem($item)
    {
        if ($item instanceof Item) {
            self::$items[$item->getCustomName()] = $item;
        }
    }

    public static function getItem(string $itemName)
    {
        if (array_key_exists($itemName, self::$items)) {
            return self::$items[$itemName];
        }
        return false;
    }

    public static function createItems(array $tiers)
    {

        $items = [
            'diamond_sword' => [Item::DIAMOND_SWORD, TieredTool::TIER_DIAMOND],
            'diamond_hoe' => [Item::DIAMOND_HOE, TieredTool::TIER_DIAMOND],
            'diamond_axe' => [Item::DIAMOND_AXE, TieredTool::TIER_DIAMOND],
            'diamond_shovel' => [Item::DIAMOND_SHOVEL, TieredTool::TIER_DIAMOND],
            'diamond_pickaxe' => [Item::DIAMOND_PICKAXE, TieredTool::TIER_DIAMOND],
            'diamond_helmet' => [Item::DIAMOND_HELMET, TieredTool::TIER_DIAMOND],
            'diamond_chestplate' => [Item::DIAMOND_CHESTPLATE, TieredTool::TIER_DIAMOND],
            'diamond_leggings' => [Item::DIAMOND_LEGGINGS, TieredTool::TIER_DIAMOND],
            'diamond_boots' => [Item::DIAMOND_BOOTS, TieredTool::TIER_DIAMOND],
            'stone_sword' => [Item::STONE_SWORD],
            'stone_hoe' => [Item::STONE_HOE],
            'stone_axe' => [Item::STONE_AXE],
            'stone_shovel' => [Item::STONE_SHOVEL],
            'stone_pickaxe' => [Item::STONE_PICKAXE],
            'iron_helmet' => [Item::IRON_HELMET],
            'iron_chestplate' => [Item::IRON_CHESTPLATE],
            'iron_leggings' => [Item::IRON_LEGGINGS],
            'iron_boots' => [Item::IRON_BOOTS],
            'iron_sword' => [Item::IRON_SWORD],
            'iron_hoe' => [Item::IRON_HOE],
            'iron_axe' => [Item::IRON_AXE],
            'iron_shovel' => [Item::IRON_SHOVEL],
            'iron_pickaxe' => [Item::IRON_PICKAXE],
            'gold_helmet' => [Item::GOLD_HELMET],
            'gold_chestplate' => [Item::GOLD_CHESTPLATE],
            'gold_leggings' => [Item::GOLD_LEGGINGS],
            'gold_boots' => [Item::GOLD_BOOTS],
            'gold_sword' => [Item::GOLD_SWORD],
            'gold_hoe' => [Item::GOLD_HOE],
            'gold_axe' => [Item::GOLD_AXE],
            'gold_shovel' => [Item::GOLD_SHOVEL],
            'gold_pickaxe' => [Item::GOLD_PICKAXE],
        ];
        //TODO ARMOR ITEMS NEED TO BE FIXED
        foreach ($tiers as $key => $tier) {
            foreach ($tier['items'] as $key1 => $item) {
                self::addItem(new UncommonTierItem($items[$key1][0], 0, $item['name'], $items[$key1][1], $item['bonuses']));
            }
        }
    }
}