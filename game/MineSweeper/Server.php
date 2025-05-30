<?php

namespace Game\MineSweeper;

use Game\Exception\GameException;

class Server
{
    private array $players;
    public function __construct(public string $serverId) {}

    public function start(): void
    {
        
    }

    public function getServerId() : string
    {
        return $this->serverId;
    }

    public function addPlayer(Player $player): void
    {
        $this->players[$player->getPlayerId()] = $player;
    }

    public function getPlayer(?string $playerId = null) : Player|array
    {
        if ($playerId) {
            if (!isset($this->players[$playerId])) throw new GameException("Такого игрока нет");
            return $this->players[$playerId];
        }else{
            return $this->players;
        }
    }
}