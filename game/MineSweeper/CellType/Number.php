<?php

namespace Game\MineSweeper\CellType;

use Game\MineSweeper\Cell;

class Number extends Cell
{
    private int $bombNear;

    public function setBombNear(int $bombNear): void
    {
        $bombNear = min(max($bombNear, 0), 8);
        $this->bombNear = $bombNear;  
    }

    public function getBombNear(): int
    {
        return $this->bombNear;   
    }
}