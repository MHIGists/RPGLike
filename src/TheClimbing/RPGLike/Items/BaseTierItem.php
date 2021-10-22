<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\CompoundTag;
use TheClimbing\RPGLike\RPGLike;


class BaseTierItem extends TieredTool {
    public string $item_tier;
    public array $lore;
    public string $glow_colour;
    public array $bonus;
    public array $available_bonuses = [
      'damage',
      'health',
      'defense',
      'movement_speed',
      'mining_speed',
      'jump_power',
      'mana' //add mana for active skills??
    ];
    public function __construct(int $id, int $meta, string $name, int $tier,string $item_tier, array $bonus)
    {
        parent::__construct($id, $meta, $name, $tier);
        $this->item_tier = $item_tier;
        if (array_search($bonus[array_key_first($bonus)],$this->available_bonuses) != false){
            $this->bonus = $bonus;
        }else{
            RPGLike::getInstance()->getLogger()->alert('All available bonuses are: damage, health, defense, movement_speed, mining_speed, jump_power, mana');
            $this->bonus = ['damage' => 1];
        }
    }

    public function setEnchantGlow(){
        $ench = new ListTag(Item::TAG_ENCH, [], NBT::TAG_Compound);
        $this->setNamedTagEntry($ench);
    }
    public function getItemBonus(): array
    {
        return $this->bonus;
    }
    public function setCustomLore(string $tier)
    {
        $lore = RPGLike::getInstance()->getTieredItems()[$tier][$this->getCustomName()];

    }
}