<?php

namespace TheClimbing\RPGLike\Items;

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