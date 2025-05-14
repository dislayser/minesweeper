<?php

namespace Game\MineSweeper\CellType;

use Game\MineSweeper\Cell;

class Bomb extends Cell
{
    public function isBomb(): bool
    {
        return true;
    }
}