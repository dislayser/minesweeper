<?php

declare(strict_types=1);

namespace Game\MineSweeper\CellType;

trait CellsNear
{ 
    /** @var Cell[] $neer  */
    private array $neer = [];
    // GETTERS
    public function getTopLeft() : Cell
    {
        return $this->getCellNeer(0);
    }

    private function getCellNeer(int $index): ?Cell
    {
        if (isset($this->neer[$index])){
            return $this->neer[$index];
        }
        return null;
    }

    // SETTERS 
}