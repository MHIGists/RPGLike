<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\CompoundTag;

trait BaseTieredItem
{
    public  $tier;
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
    public function setEnchantGlow(){
        $ench = new ListTag(self::TAG_ENCH, [], NBT::TAG_Compound);
        $this->setNamedTagEntry($ench);
    }
}