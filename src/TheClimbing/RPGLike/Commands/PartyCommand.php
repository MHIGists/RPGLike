<?php

namespace TheClimbing\RPGLike\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Systems\PartySystem;

class PartyCommand extends Command
{
    private RPGLike $source;
    public function __construct(RPGLike $RPGLike)
    {
        parent::__construct('party', 'Send,accept or deny party requests','/party <playerName>|<accept>|<deny>|<create>');
        $this->source = $RPGLike;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (empty($args) || $args[0] == '' || $args[0] == ' ') {
            $sender->sendMessage($this->getUsage());
        } else {
            $args = array_map('strtolower', $args);
            switch ($args[0]) {
                case 'accept':
                    if ($sender->hasPartyInvite()){
                        PartySystem::getPlayerParty($sender->getPartyInvite())->addPlayerInParty($sender);
                    }
                    break;
                case 'deny':
                    $sender->removePartyInvite();
                    break;
                case 'create':
                    if (!empty($args[1])){
                        PartySystem::createParty($args[1], $sender, 4);
                    }else{
                        PartySystem::createParty($sender->getName(), $sender, 4); //TODO add configuration for party size
                    }
                    break;
                default:
                    $targetPlayer = $this->source->getServer()->getPlayer($args[0]);
                    if ($targetPlayer != null){
                        if ($targetPlayer->getParty() == false){
                            if ($sender->getParty()){
                                $targetPlayer->sendPartyInvite($sender->getParty());
                            }else{
                                $sender->sendMessage('You need to be in a party to send invites.');
                            }
                        }else{
                            $sender->sendMessage('Player already in a party');
                        }
                    }else{
                        $sender->sendMessage('The player is offline or is already in party');
                    }
            }
        }
    }
}