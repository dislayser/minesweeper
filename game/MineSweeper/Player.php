<?php

namespace Game\MineSweeper;

class Player
{
    public function __construct(
        private ?string $playerId = null
    ) {
        if (!$this->playerId){
            if (session_status() === PHP_SESSION_NONE || empty($_SESSION)) session_start();
            $this->playerId = session_id();
        }
    }

    public function getPlayerId() : string
    {
        return $this->playerId;
    }
}