<?php


namespace TheClimbing\RPGLike;


use pocketmine\item\ItemIds;

class Utils
{


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
    public static function translateStringToItem(string $item) : array {
        $items = [
          'stone_sword' => [ItemIds::STONE_SWORD,0,'Uncommon Sword',['damage'=>1]] ,
          'stone_axe' => [ItemIds::STONE_AXE,0,'Uncommon Axe',['damage' => 1]],
          'stone_picaxe' => [ItemIds::STONE_PICKAXE,0,'Uncommon Picaxe',['damage' =>1]],
          'stone_shovel' => [ItemIds::STONE_SHOVEL,0,'Uncommon Shovel', ['damage' => 1]],
          'iron_sword' => [],
          'iron_axe' => [],
          'iron_picaxe' => [],
          'iron_shovel' => [],
          'gold_sword' => [],
          'gold_axe'=>[],
          'gold_picaxe' => [],
          'gold_shovel' => [],
          'diamond_sword' => [],
          'diamond_axe' => [],
          'diamond_picaxe' => [],
          'diamond_shovel' => [],
            'leather_helmet' => [],
            'leather_chestplate' => [],
            'leather_leggings' => [],
            'leather_boots' => [],
            'iron_helmet'=> [],
            'iron_chestplate' => [],
            'iron_leggings' => [],
            'iron_boots' => [],
            'gold_helmet' => [],
            'gold_chestplate' => [],
            'gold_leggings' => [],
            'gold_boots' => [],
            'diamond_helmet' => [],
            'diamond_chestplate' => [],
            'diamond_leggings' => [],
            'diamond_boots' => []
        ];
        return [];
    }
}