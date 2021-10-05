<?php

namespace TheClimbing\RPGLike\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;

class RPGCommand extends Command implements PluginIdentifiableCommand
{
    private $loader;

    public function __construct(RPGLike $rpg)
    {
        parent::__construct('rpg');
        $this->loader = $rpg;
        $this->setDescription('Opens RPG Menu');
        $this->setPermission('rpgcommand');
        $this->setUsage('rpg stats|skills|upgrade or rpg help <skillName>');
        $this->setPermissionMessage('You dont have permission to use this command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof RPGPlayer && $sender->hasPermission($this->getPermission()) || $sender->isOp()) {
            if (empty($args) || $args[0] == '' || $args[0] == ' ') {
                RPGForms::menuForm($sender);
            } else {
                $args = array_map('strtolower', $args);
                switch ($args[0]) {
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
                        RPGForms::upgradeStatsForm($sender);
                }
            }
        }
    }

    public function getPlugin(): Plugin
    {
        return $this->loader;
    }
}
