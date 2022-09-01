<?php


namespace TheClimbing\RPGLike;


use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;

class Utils
{

    public static array $items;
    public static function parseKeywords(array $keywords, string $subject): string
    {
        $subject = str_replace(['{', '}'], [' ', ' '], $subject);
        foreach ($keywords as $key => $value) {
            $subject = str_replace($key, $value, $subject);
        }
        return $subject;
    }

    public static function parseArrayKeywords(array $keywords, array $messages)
    {
        foreach ($messages as $key => $message) {
            if (is_array($message)) {
                foreach ($message as $key1 => $value) {
                    $messages[$key][$key1] = self::parseKeywords($keywords, $value);
                }
            } else {
                $messages[$key] = self::parseKeywords($keywords, $message);
            }
        }
        return $messages;
    }
    //TODO Those would ultimately all come from a config
    public static function getItems() : array{
        if (isset(self::$items)){
            return self::$items;
        }else{
            $items = [
                'stone_sword' => [ItemIds::STONE_SWORD, 0, 'Uncommon Sword', ToolTier::STONE() ,['damage' => 1]],
                'stone_axe' => [ItemIds::STONE_AXE, 0, 'Uncommon Axe', ToolTier::STONE() ,['damage' => 1]],
                'stone_pickaxe' => [ItemIds::STONE_PICKAXE, 0, 'Uncommon Pickaxe', ToolTier::STONE() ,['damage' => 1]],
                'stone_shovel' => [ItemIds::STONE_SHOVEL, 0, 'Uncommon Shovel', ToolTier::STONE() ,['damage' => 1]],
                'iron_sword' => [ItemIds::IRON_SWORD,0,'Rare Sword', ToolTier::IRON() ,['damage' => 1]],
                'iron_axe' => [ItemIds::IRON_AXE,0,'Rare Axe', ToolTier::IRON() ,['damage' => 1]],
                'iron_pickaxe' => [ItemIds::IRON_PICKAXE,0,'Rare Pickaxe', ToolTier::IRON() ,['damage' => 1]],
                'iron_shovel' => [ItemIds::IRON_SHOVEL,0,'Rare Shovel', ToolTier::IRON() ,['damage' => 1]],
                'gold_sword' => [ItemIds::GOLD_SWORD,0,'Mythic Sword', ToolTier::GOLD() ,['damage' => 1]],
                'gold_axe' => [ItemIds::GOLD_AXE,0,'Mythic Axe', ToolTier::GOLD() ,['damage' => 1]],
                'gold_pickaxe' => [ItemIds::GOLD_PICKAXE,0,'Mythic Pickaxe', ToolTier::GOLD() ,['damage' => 1]],
                'gold_shovel' => [ItemIds::GOLD_SHOVEL,0,'Mythic Shovel', ToolTier::GOLD() ,['damage' => 1]],
                'diamond_sword' => [ItemIds::DIAMOND_SWORD,0,'Epic Sword', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_axe' => [ItemIds::DIAMOND_AXE,0,'Epic Axe', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_pickaxe' => [ItemIds::DIAMOND_PICKAXE,0,'Epic Pickaxe', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_shovel' => [ItemIds::DIAMOND_SHOVEL,0,'Epic Shovel', ToolTier::DIAMOND() ,['damage' =>1]],
                'iron_helmet' => [ItemIds::IRON_HELMET,0,'Uncommon Helmet',['defense' => 1]],
                'iron_chestplate' => [ItemIds::IRON_CHESTPLATE,0,'Uncommon Chestplate',['defense' => 1]],
                'iron_leggings' => [ItemIds::IRON_LEGGINGS,0,'Uncommon Leggings',['defense' => 1]],
                'iron_boots' => [ItemIds::IRON_BOOTS,0,'Uncommon Boots',['defense' => 1]],
                'gold_helmet' => [ItemIds::GOLD_HELMET,0,'Mythic Helmet',['defense' => 1]],
                'gold_chestplate' => [ItemIds::GOLD_CHESTPLATE,0,'Mythic Chestplate',['defense' => 1]],
                'gold_leggings' => [ItemIds::GOLD_LEGGINGS,0,'Mythic Leggings',['defense' => 1]],
                'gold_boots' => [ItemIds::GOLD_BOOTS,0,'Mythic Boots',['defense' => 1]],
                'diamond_helmet' => [ItemIds::DIAMOND_HELMET,0,'Rare Helmet',['defense' => 1]],
                'diamond_chestplate' => [ItemIds::DIAMOND_CHESTPLATE,0,'Rare Chestplate',['defense' => 1]],
                'diamond_leggings' => [ItemIds::DIAMOND_LEGGINGS,0,'Rare Leggings',['defense' => 1]],
                'diamond_boots' => [ItemIds::DIAMOND_BOOTS,0,'Rare Boots',['defense' => 1]],
                'chain_helmet' => [ItemIds::CHAIN_HELMET,0,'Epic Helmet',['defense' => 1]],
                'chain_chestplate' => [ItemIds::CHAIN_CHESTPLATE,0,'Epic Chestplate',['defense' => 1]],
                'chain_leggings' => [ItemIds::CHAIN_LEGGINGS,0,'Epic Leggings',['defense' => 1]],
                'chain_boots' => [ItemIds::CHAIN_BOOTS,0,'Epic Boots',['defense' => 1]],
            ];
            self::$items = $items;
            return $items;
        }
    }
    public static function translateStringToItem(string $item): array|null
    {
        $items = self::getItems();
        if (array_key_exists($item, $items)){
            return $items[$item];
        }
        return null;
    }
}