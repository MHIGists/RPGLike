<?php


namespace TheClimbing\RPGLike\Commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use TheClimbing\RPGLike\Players\RPGPlayer;

class LevelUpCommand extends Command
{
    public function __construct()
    {
        parent::__construct('lvlup');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof RPGPlayer) {
            $xpmanager = $sender->getXpManager();
            if (empty($args)) {
                $xpmanager->setXpLevel($xpmanager->getXpLevel() + 1);
            } else {
                $xpmanager->setXpLevel($args[0]);
            }
        }
    }
}