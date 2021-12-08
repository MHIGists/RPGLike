<?php

namespace TheClimbing\RPGLike\Items\Armor;

use pocketmine\item\DiamondHelmet;
use pocketmine\utils\Color;

class CustomDiamondHelmet extends DiamondHelmet
{
    use BaseCustomArmor;
    public function __construct(string $tier, int $meta = 0)
    {
        parent::__construct($meta);
        switch ($tier){
            case 'uncommon':
                $this->setCustomColor(new Color(0,170,0));
                break;
            case 'rare':
                break;
            case 'epic':
                $this->setCustomColor(new Color(170, 45, 255));
                break;
            case 'mythic':
                $this->setCustomColor(new Color(255, 0, 0));
                break;
        }
        $this->setEnchantGlow();
    }
}