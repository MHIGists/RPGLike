<?php


namespace TheClimbing\RPGLike\Tasks;


use pocketmine\scheduler\Task;
use TheClimbing\RPGLike\Players\RPGPlayer;

class CooldownTask extends Task
{
    private $player;
    private $skillName;

    public function __construct(RPGPlayer $player, string $skillName)
    {
        $this->player = $player;
        $this->skillName = $skillName;
    }

    public function onRun(int $currentTick)
    {
        $this->player->getSkill($this->skillName)->removeCooldown();
        $this->player->sendMessage($this->skillName . ' off cooldown');
    }
}