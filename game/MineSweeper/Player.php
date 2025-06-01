<?php

namespace Game\MineSweeper;

use Game\Exception\GameException;
use Game\Exception\PlayerDieException;


class Player
{
    private bool $die = false;
    private bool $win = false;

    public function __construct(
        private ?string $playerId = null
    ) {
        if (!$this->playerId){
            if (session_status() === PHP_SESSION_NONE) session_start();
            $this->playerId = session_id();
        }
    }

    public function getPlayerId() : string
    {
        return $this->playerId;
    }

    public function win() : bool
    {
        if ($this->die) throw new PlayerDieException("Этот игрок проиграл.");
        $this->win = true;
        return true;
    }

    public function die() : bool
    {
        if ($this->win) throw new GameException("Этот игрок выиграл.");
        $this->die = true;
        return true;
    }

    public function isWin() : bool
    {
        return $this->win;
    }

    public function isDie() : bool
    {
        return $this->die;
    }
}