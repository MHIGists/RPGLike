<?php

namespace TheClimbing\RPGLike\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use TheClimbing\RPGLike\RPGLike;

class PartyCommand extends Command
{
    private $source;
    public function __construct(RPGLike $RPGLike)
    {
        parent::__construct('party', 'Send,accept or deny party requests','/party <playerName>|<accept>|<deny>');
        $this->source = $RPGLike;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (empty($args) || $args[0] == '' || $args[0] == ' ') {
            $sender->sendMessage($this->getUsage());
        } else {
            $args = array_map('strtolower', $args);
            switch ($args) {
                case "accept":
                    //TODO Finish party command
            }
        }
    }
}