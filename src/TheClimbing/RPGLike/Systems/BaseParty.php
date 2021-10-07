<?php

namespace TheClimbing\RPGLike\Systems;

use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Players\RPGPlayer;

class BaseParty
{
    /**
     * @var RPGPlayer[]
     */
    private array $party_players;
    private string $party_owner;
    private string $partyName;

    /**
     * @param string $partyName
     * @param RPGPlayer $party_owner
     * @param int $party_size
     */
    public function __construct(string $partyName, RPGPlayer $party_owner, int $party_size)
    {
        $this->party_players[$party_owner->getName()] = $party_owner;
        for($i = 1;$i<$party_size;$i++) {
            $party_players[] = '';
        }
        $this->party_owner = $party_owner->getName();
        $this->partyName = $partyName;
        $party_owner->partyName = $partyName;
    }
    public function getPartyMembers(): array
    {
        return $this->party_players;
    }
    public function playerInThisParty(string $player): bool
    {
        return array_key_exists($player, $this->party_players);
    }
    public function removePlayer(string $playerName){
        $this->party_players[$playerName]->sendMessage("You've left the party");
        unset($this->party_players[$playerName]);
    }
    public function addPlayerInParty(RPGPlayer $player){
        $this->party_players[$player->getName()] = $player;
        $player->partyName = $this->partyName;
    }
    public function isPartyOwner(string $playerName): bool
    {
        return $playerName == $this->party_owner;
}
    //TODO make this use player classes instead of names
    //TODO store base parties in party system each party will have for key in the array the player that opened the party
}