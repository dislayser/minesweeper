<?php

namespace Game\MineSweeper\CellType;

use Game\MineSweeper\Cell;

class Number extends Cell
{
    private int $bombNear;

    public function setBombNear($bombNear): void
    {
        $this->bombNear = $bombNear;  
    }

    public function getBombNear(): int
    {
        return $this->bombNear;   
    }
}