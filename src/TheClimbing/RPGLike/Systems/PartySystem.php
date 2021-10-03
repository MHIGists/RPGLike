<?php

namespace TheClimbing\RPGLike\Systems;

use TheClimbing\RPGLike\RPGLike;

class PartySystem
{
    private $parties = [];
    private $plugin;
    public function __construct(RPGLike $plugin)
    {
        $this->plugin = $plugin;
    }
    public function createParty(array $pending_party)
    {
        $this->parties[] = $pending_party;
    }
    public function isPlayerInParty(string $playerName){
        foreach ($this->parties as $party) {
            if (array_search($playerName, $party) != false){
                return true;
            }
        }
        return false;
    }
    public function removePlayerFromParty(string $playerName){
        foreach ($this->parties as $party) {
            $result = array_search($playerName, $party);
            if ($result != false){
                unset($party[$result]);
                return true;
            }
        }
        return false;
    }
    public function addPlayerInParty(string $sourcePlayer, string $targetPlayer){
        foreach ($this->parties as $party) {
            $result = array_search($sourcePlayer, $party);
            if ($result != false){
                $party[] = $targetPlayer;
            }
        }
    }
    //TODO use this only as a manager for parties move this to Base Party
}