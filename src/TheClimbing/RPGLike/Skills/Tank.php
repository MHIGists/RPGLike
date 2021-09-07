<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Skills;


use pocketmine\level\biome\UnknownBiome;
use TheClimbing\RPGLike\Players\RPGPlayer;

class Tank extends BaseSkill
{
    public array $config = [];
    public function __construct(RPGPlayer $owner)
    {
        $this->config = $owner->getConfig()->getNested('Skills')['Tank']['levels'];

        $this->setType('passive');
        $this->setCooldownTime(0);
        $this->setRange(0);
        parent::__construct($owner, 'Tank', $this->config);
    }

    public function setPlayerHealth(RPGPlayer $player)
    {
        $health = $player->getMaxHealth();
        $player->setMaxHealth((int)($health * 1.15));
    }
}