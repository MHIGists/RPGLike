<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;

abstract class BaseTieredItem extends Item
{
    public string $tier;
    public array $lore;
    public string $glow_colour;
    public array $available_bonuses = [
      'damage',
      'health',
      'defense',
      'movement_speed',
      'mining_speed',
      'jump_power',
      'mana' //add mana for active skills??
    ];
    public function __construct()
    {

    }
}