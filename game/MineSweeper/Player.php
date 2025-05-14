<?php

namespace Game\MineSweeper;

use Exception;

class Player
{
    private bool $die = false;
    private bool $win = false;

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

    protected function win() : bool
    {
        if ($this->die) throw new Exception("Этот игрок проиграл.");
        $this->win = true;
        return true;
    }

    protected function die() : bool
    {
        if ($this->win) throw new Exception("Этот игрок выиграл.");
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