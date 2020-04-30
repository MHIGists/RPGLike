<?php


namespace TheClimbing\RPGLike\Tasks;


use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\Players\PlayerManager;
use TheClimbing\RPGLike\Players\RPGPlayer;

class CooldownTask extends Task
{
    private $player;


    public function __construct(RPGPlayer $player)
    {
        $this->player = $player;
    }

    public function onRun(int $currentTick)
    {
        $this->player->removeCooldown();
    }
}