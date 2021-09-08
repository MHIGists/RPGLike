<?php


namespace TheClimbing\RPGLike\Tasks;


use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;

class HudTask extends Task
{
    public $main;

    public function __construct(RPGLike $main)
    {
        $this->main = $main;
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun() : void
    {
        $players = $this->main->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            if ($player instanceof RPGPlayer)
            $player->sendPopup($this->main->getHUD($player));
        }
    }
}