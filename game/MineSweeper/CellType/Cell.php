<?php

declare(strict_types=1);

namespace Game\MineSweeper\CellType;

use Game\Exception\GameException;

class Cell
{
    use CellsNear;
    private int $x;
    private int $y;
    private bool $opened = false;
    private bool $flag = false;
    private bool $bomb = false;
    
    public function __construct(int $x, int $y)
    {
        $this->x = max($x, 0);
        $this->y = max($y, 0);
    }

    public function open(): static
    {
        if (!$this->isOpen()){
            $this->opened = true;
        }else if ($this->isBomb()){
            throw new GameException("Это бомба.");
        }
        return $this;
    }

    public function setFlag(): static
    {
        if ($this->isOpen()){
            throw new GameException("Ячейка {$this->x}x{$this->y} уже открыта.");
        }
        if ($this->isOpen() && $this->isBomb()){
            throw new GameException("Ячейка {$this->x}x{$this->y} уже открыта (бомба).");
        }
        $this->flag = true;
        return $this;
    }

    public function setBomb(): static
    {
        if (!$this->isOpen()) {
            $this->bomb = true;
        } else {
            throw new GameException("Ячейка {$this->x}x{$this->y} уже открыта.");
        }
        return $this;
    }

    public function isOpen(): bool
    {
        return $this->opened;
    }

    public function isFlag() : bool
    {
        return $this->flag;
    }

    public function isBomb() : bool
    {
        return $this->bomb;
    }
}