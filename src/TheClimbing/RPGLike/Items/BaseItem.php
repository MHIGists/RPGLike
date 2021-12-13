<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;

use TheClimbing\RPGLike\RPGLike;


trait BaseItem
{
    public string $item_tier;
    public array $custom_lore;
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

    public function init(string $item_tier, array $bonus)
    {
        $this->item_tier = 'temp';
        if (array_search(array_key_first($bonus), $this->available_bonuses) != false) {
            $this->bonus = $bonus;
        } else {
            RPGLike::getInstance()->getLogger()->alert('All available bonuses are: damage, health, defense, movement_speed, mining_speed, jump_power, mana');
            $this->bonus = ['damage' => 1];
        }
        $this->setEnchantGlow();
    }


    public function setEnchantGlow(){}

    public function getItemBonus(): array
    {
        return $this->bonus;
    }

    public function setCustomLore(string $tier)
    {
        $original_lore = $this->getLore();
    }
}