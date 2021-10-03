<?php

namespace TheClimbing\RPGLike\Systems;

use TheClimbing\RPGLike\Players\RPGPlayer;

class PartySystem
{
    private int $party_size = 0;
    private array $party_players = [];

    /**
     * @param array $party_players
     * @param int $party_size
     */
    public function __construct(array $party_players, int $party_size)
    {
        $this->party_players = $party_players;
        $this->party_size = $party_size;
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

}