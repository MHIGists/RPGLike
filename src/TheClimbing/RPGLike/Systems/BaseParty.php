<?php

namespace TheClimbing\RPGLike\Systems;

class BaseParty
{
    private array $party_players;
    private string $party_owner;
    /**
     * @param array $party_players
     * @param int $party_size
     */
    public function __construct(array $party_players, int $party_size)
    {
        $this->party_players = $party_players;
        for($i = 0;$i<$party_size;$i++) {
            $party_players[] = '';
        }
    }
    public function getPartyMembers(): array
    {
        return $this->party_players;
    }
    public function playerInThisParty(string $player): bool
    {
        return array_key_exists($player, $this->party_players);
    }
    public function propagatePartyToMembers(){
        foreach ($this->party_players as $party_player) {
            $party_player->registerParty($this->party_players);
        }
    }
    //TODO make this use player classes instead of names
    //TODO store base parties in party system each party will have for key in the array the player that opened the party
}