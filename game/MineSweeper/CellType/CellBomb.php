<?php

declare(strict_types=1);

namespace Game\MineSweeper\CellType;

use Game\MineSweeper\AbstractCell;

class CellBomb extends AbstractCell
{
    public function isBomb(): bool
    {
        return true;
    }
}