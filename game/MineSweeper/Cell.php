<?php

namespace Game\MineSweeper;

class Cell implements CellInterface
{
    public function __construct(
        private int $x,
        private int $y,
    ) {}

    public function getX() : int
    {
        return $this->x;
    }

    public function getY() : int
    {
        return $this->y;
    }
    
    public function isBomb(): bool
    {
        return false;
    }
}