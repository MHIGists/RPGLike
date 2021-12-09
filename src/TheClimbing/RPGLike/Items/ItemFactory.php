<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;

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
        foreach (Utils::getItems() as $item) {
            self::addItem($item);
        }
    }
}