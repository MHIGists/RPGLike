<?php

namespace TheClimbing\RPGLike\Items;

use pocketmine\item\Sword;
use TheClimbing\RPGLike\ItemSets\JuggernautSet;

class UncommonSword extends Sword implements UncommonItem, JuggernautSet {
    public function __construct(int $id, int $meta, string $name, int $tier)
    {
        parent::__construct($id, $meta, $name, $tier);
    }
}