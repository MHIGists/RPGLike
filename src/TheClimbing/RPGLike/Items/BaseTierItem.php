<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Axe;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\CompoundTag;
use TheClimbing\RPGLike\RPGLike;

$class = RPGLike::getInstance()->currentClass;
if ($class == 'Sword'){
    class BaseTierItem extends Sword {}
}elseif ($class == 'Axe'){
    class BaseTierItem extends Axe{}
}
class BaseTierItem{
    public string $tier;
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
    public function __construct(string $tier, array $bonus)
    {
        $this->tier = $tier;
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