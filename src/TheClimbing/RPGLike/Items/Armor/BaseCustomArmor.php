<?php

namespace TheClimbing\RPGLike\Items\Armor;

use TheClimbing\RPGLike\RPGLike;

trait BaseCustomArmor
{
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
    public function init(string $item_tier, array $bonus){
        $this->item_tier = $item_tier;
        if (array_search($bonus[array_key_first($bonus)],$this->available_bonuses) != false){
            $this->bonus = $bonus;
        }else{
            RPGLike::getInstance()->getLogger()->alert('All available bonuses are: damage, health, defense, movement_speed, mining_speed, jump_power, mana');
            $this->bonus = ['damage' => 1];
        }
    }
    public function getItemBonus(): array
    {
        return $this->bonus;
    }
    public function setCustomLore(string $tier)
    {
        $lore = RPGLike::getInstance()->getTieredItems()[$tier][$this->getCustomName()];
    }
    //todo
}