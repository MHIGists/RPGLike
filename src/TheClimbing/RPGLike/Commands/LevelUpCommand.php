<?php


namespace TheClimbing\RPGLike\Commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
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
            if (empty($args)) {
                $sender->setXpLevel($sender->getXpManager()->getXpLevel() + 1);
            } else {
                $sender->setXpLevel($args[0]);
            }
        }
    }
}