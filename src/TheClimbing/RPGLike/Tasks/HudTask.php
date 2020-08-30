<?php


namespace TheClimbing\RPGLike\Tasks;


use pocketmine\scheduler\Task;
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
     * @param int $currentTick
     * @return void
     */
    public function onRun(int $currentTick)
    {
        $players = $this->main->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            $player->sendPopup($this->main->getHUD($player));
        }
    }
}