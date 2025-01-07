<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use TheClimbing\RPGLike\Utils;

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
        $item_arrays = Utils::getItems();
        $items = [];
        foreach ($item_arrays as $key => $item) {
            if ($key == 'stone_sword') {
                new BaseSword(new ItemIdentifier($item[0], $item[1]), $item[2], $item[3], $item[4]);
            }
        }
    }
}