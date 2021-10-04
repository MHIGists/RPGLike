<?php

namespace TheClimbing\RPGLike\Systems;

use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;

class PartySystem
{
    /**
     * @var BaseParty[]
     */
    private static array $parties = [];
    private static RPGLike $plugin;

    public function __construct(RPGLike $plugin)
    {
        self::$plugin = $plugin;
    }
    public static function createParty(string $name, RPGPlayer $owner, int $party_size)
    {
        self::$parties[$name][] = new BaseParty($name, $owner, $party_size );
    }
    public static function removeParty(string $partyName){
        unset(self::$parties[$partyName]);
    }
    public static function getPlayerParty(string $playerName){
        foreach (self::$parties as $party) {
            if ($party->playerInThisParty($playerName)){
                return $party;
            }
        }
    }
}