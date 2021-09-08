<?php


namespace TheClimbing\RPGLike\Tasks;

use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\Players\RPGPlayer;

class CooldownTask extends Task
{
    private RPGPlayer $player;
    private string $skillName;

    public function __construct(RPGPlayer $player, string $skillName)
    {
        $this->player = $player;
        $this->skillName = $skillName;
    }


    public function onRun(): void
    {
        $this->player->getSkill($this->skillName)->removeCooldown();
        $this->player->sendMessage($this->skillName . ' off cooldown');
    }
}