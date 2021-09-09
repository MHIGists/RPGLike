<?php

namespace TheClimbing\RPGLike\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\RPGPlayer;

class RPGCommand extends Command
{

    public function __construct()
    {
        parent::__construct('rpg');
        $this->setDescription('Opens RPG Menu');
        $this->setUsage('rpg stats|skills|upgrade or rpg help <skillName>');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof RPGPlayer && $sender->hasPermission($this->getPermission()) || $sender->isOp()) {
            if (empty($args) || $args[0] == '' || $args[0] == ' ') {
                RPGForms::menuForm($sender);
            } else {
                $args = array_map('strtolower', $args);
                switch ($args) {
                    case "stats":
                        RPGForms::statsForm($sender);
                        break;
                    case "skills":
                        RPGForms::skillsHelpForm($sender);
                        break;
                    case "help":
                        if (array_key_exists(1, $args)) {
                            RPGForms::skillHelpForm($sender, $args[1]);
                        } else {
                            $sender->sendMessage($this->getUsage());
                        }
                        break;
                    case "upgrade":
                        RPGForms::upgradeStatsForm($sender, 0);
                }
            }
        } else {
            $sender->sendMessage($this->getPermissionMessage());
        }
    }
}
