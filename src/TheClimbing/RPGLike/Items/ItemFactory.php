<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\DiamondBoots;
use pocketmine\item\DiamondChestplate;
use pocketmine\item\DiamondHelmet;
use pocketmine\item\DiamondLeggings;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldChestplate;
use pocketmine\item\GoldHelmet;
use pocketmine\item\GoldLeggings;
use pocketmine\item\IronBoots;
use pocketmine\item\IronChestplate;
use pocketmine\item\IronHelmet;
use pocketmine\item\IronLeggings;
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

        $tiered = [
            'diamond_sword' => [Item::DIAMOND_SWORD, TieredTool::TIER_DIAMOND],
            'diamond_hoe' => [Item::DIAMOND_HOE, TieredTool::TIER_DIAMOND],
            'diamond_axe' => [Item::DIAMOND_AXE, TieredTool::TIER_DIAMOND],
            'diamond_shovel' => [Item::DIAMOND_SHOVEL, TieredTool::TIER_DIAMOND],
            'diamond_pickaxe' => [Item::DIAMOND_PICKAXE, TieredTool::TIER_DIAMOND],
            'stone_sword' => [Item::STONE_SWORD, TieredTool::TIER_STONE],
            'stone_hoe' => [Item::STONE_HOE], TieredTool::TIER_STONE,
            'stone_axe' => [Item::STONE_AXE, TieredTool::TIER_STONE],
            'stone_shovel' => [Item::STONE_SHOVEL, TieredTool::TIER_STONE],
            'stone_pickaxe' => [Item::STONE_PICKAXE, TieredTool::TIER_STONE],
            'iron_sword' => [Item::IRON_SWORD, TieredTool::TIER_STONE],
            'iron_hoe' => [Item::IRON_HOE, TieredTool::TIER_STONE],
            'iron_axe' => [Item::IRON_AXE, TieredTool::TIER_STONE],
            'iron_shovel' => [Item::IRON_SHOVEL, TieredTool::TIER_STONE],
            'iron_pickaxe' => [Item::IRON_PICKAXE, TieredTool::TIER_STONE],
            'gold_sword' => [Item::GOLD_SWORD, TieredTool::TIER_GOLD],
            'gold_hoe' => [Item::GOLD_HOE, TieredTool::TIER_GOLD],
            'gold_axe' => [Item::GOLD_AXE, TieredTool::TIER_GOLD],
            'gold_shovel' => [Item::GOLD_SHOVEL, TieredTool::TIER_GOLD],
            'gold_pickaxe' => [Item::GOLD_PICKAXE, TieredTool::TIER_GOLD],
        ];
        $armor = [
            'diamond_helmet' => new DiamondHelmet(),
            'diamond_chestplate' => new DiamondChestplate(),
            'diamond_leggings' => new DiamondLeggings(),
            'diamond_boots' => new DiamondBoots(),
            'iron_helmet' => new IronHelmet(),
            'iron_chestplate' => new IronChestplate(),
            'iron_leggings' => new IronLeggings(),
            'iron_boots' => new IronBoots(),
            'gold_helmet' => new GoldHelmet(),
            'gold_chestplate' => new GoldChestplate(),
            'gold_leggings' => new GoldLeggings(),
            'gold_boots' => new GoldBoots(),
        ];

        //TODO ARMOR ITEMS NEED TO BE FIXED
        foreach ($tiers as $key => $tier) {
            foreach ($tier['items'] as $key1 => $item) {
                if (array_key_exists($key1, $tiered)){
                    self::addItem(new UncommonTierItem($tiered[$key1][0], 0, $item['name'], $tiered[$key1][1], $item['bonuses']));
                }
                if (array_key_exists($key1, $armor)){
                    self::addItem($item);
                }
            }
        }
    }
}