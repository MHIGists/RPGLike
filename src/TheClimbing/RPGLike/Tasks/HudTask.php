<?php


namespace TheClimbing\RPGLike\Tasks;


use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\RPGLike;

class HudTask extends Task
{
    public RPGLike $main;

    public function __construct(RPGLike $main)
    {
        $this->main = $main;
    }

    public function onRun() : void
    {
        $players = $this->main->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            $player->sendPopup($this->main->getHUD($player));
        }
    }
}