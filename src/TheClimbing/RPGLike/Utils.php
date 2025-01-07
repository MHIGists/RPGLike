<?php


namespace TheClimbing\RPGLike;

use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;

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

    public static function parseArrayKeywords(array $keywords, array $messages): array
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
                'stone_sword' => [VanillaItems::STONE_SWORD(), 0, 'Uncommon Sword', ToolTier::STONE() ,['damage' => 1]],
                'stone_axe' => [VanillaItems::STONE_AXE(), 0, 'Uncommon Axe', ToolTier::STONE() ,['damage' => 1]],
                'stone_pickaxe' => [VanillaItems::STONE_PICKAXE(), 0, 'Uncommon Pickaxe', ToolTier::STONE() ,['damage' => 1]],
                'stone_shovel' => [VanillaItems::STONE_SHOVEL(), 0, 'Uncommon Shovel', ToolTier::STONE() ,['damage' => 1]],
                'iron_sword' => [VanillaItems::IRON_SWORD(),0,'Rare Sword', ToolTier::IRON() ,['damage' => 1]],
                'iron_axe' => [VanillaItems::IRON_AXE(),0,'Rare Axe', ToolTier::IRON() ,['damage' => 1]],
                'iron_pickaxe' => [VanillaItems::IRON_PICKAXE(),0,'Rare Pickaxe', ToolTier::IRON() ,['damage' => 1]],
                'iron_shovel' => [VanillaItems::IRON_SHOVEL(),0,'Rare Shovel', ToolTier::IRON() ,['damage' => 1]],
                'gold_sword' => [VanillaItems::GOLDEN_SWORD(),0,'Mythic Sword', ToolTier::GOLD() ,['damage' => 1]],
                'gold_axe' => [VanillaItems::GOLDEN_AXE(),0,'Mythic Axe', ToolTier::GOLD() ,['damage' => 1]],
                'gold_pickaxe' => [VanillaItems::GOLDEN_PICKAXE(),0,'Mythic Pickaxe', ToolTier::GOLD() ,['damage' => 1]],
                'gold_shovel' => [VanillaItems::GOLDEN_SHOVEL(),0,'Mythic Shovel', ToolTier::GOLD() ,['damage' => 1]],
                'diamond_sword' => [VanillaItems::DIAMOND_SWORD(),0,'Epic Sword', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_axe' => [VanillaItems::DIAMOND_AXE(),0,'Epic Axe', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_pickaxe' => [VanillaItems::DIAMOND_PICKAXE(),0,'Epic Pickaxe', ToolTier::DIAMOND() ,['damage' =>1]],
                'diamond_shovel' => [VanillaItems::DIAMOND_SHOVEL(),0,'Epic Shovel', ToolTier::DIAMOND() ,['damage' =>1]],
                'iron_helmet' => [VanillaItems::IRON_HELMET(),0,'Uncommon Helmet',['defense' => 1]],
                'iron_chestplate' => [VanillaItems::IRON_CHESTPLATE(),0,'Uncommon Chestplate',['defense' => 1]],
                'iron_leggings' => [VanillaItems::IRON_LEGGINGS(),0,'Uncommon Leggings',['defense' => 1]],
                'iron_boots' => [VanillaItems::IRON_BOOTS(),0,'Uncommon Boots',['defense' => 1]],
                'gold_helmet' => [VanillaItems::GOLDEN_HELMET(),0,'Mythic Helmet',['defense' => 1]],
                'gold_chestplate' => [VanillaItems::GOLDEN_CHESTPLATE(),0,'Mythic Chestplate',['defense' => 1]],
                'gold_leggings' => [VanillaItems::GOLDEN_LEGGINGS(),0,'Mythic Leggings',['defense' => 1]],
                'gold_boots' => [VanillaItems::GOLDEN_BOOTS(),0,'Mythic Boots',['defense' => 1]],
                'diamond_helmet' => [VanillaItems::DIAMOND_HELMET(),0,'Rare Helmet',['defense' => 1]],
                'diamond_chestplate' => [VanillaItems::DIAMOND_CHESTPLATE(),0,'Rare Chestplate',['defense' => 1]],
                'diamond_leggings' => [VanillaItems::DIAMOND_LEGGINGS(),0,'Rare Leggings',['defense' => 1]],
                'diamond_boots' => [VanillaItems::DIAMOND_BOOTS(),0,'Rare Boots',['defense' => 1]],
                'chain_helmet' => [VanillaItems::CHAINMAIL_HELMET(),0,'Epic Helmet',['defense' => 1]],
                'chain_chestplate' => [VanillaItems::CHAINMAIL_CHESTPLATE(),0,'Epic Chestplate',['defense' => 1]],
                'chain_leggings' => [VanillaItems::CHAINMAIL_LEGGINGS(),0,'Epic Leggings',['defense' => 1]],
                'chain_boots' => [VanillaItems::CHAINMAIL_BOOTS(),0,'Epic Boots',['defense' => 1]],
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