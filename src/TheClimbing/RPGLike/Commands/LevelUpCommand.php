<?php


namespace TheClimbing\RPGLike\Commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class LevelUpCommand extends Command
{

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player)
        {
            $sender->setXpLevel($args[0]);
        }
    }
}