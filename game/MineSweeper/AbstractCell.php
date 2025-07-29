<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\Exception\PlayerDieException;

abstract class AbstractCell implements Interfaces\CellInterface
{
    private bool $flag = false;
    private bool $open = false;

    public function __construct(
        private int $col,
        private int $row,
        private int $number = 0
    ) {
        $this->number = min(9, max(0, $this->number));
    }

    public function getCol() : int
    {
        return $this->col;
    }

    public function getRow() : int
    {
        return $this->row;
    }

    public function setFlag(): static
    {
        if (!$this->isOpen()) {
            $this->flag = true;
        }
        return $this;
    }

    /**
     * @throws \Game\Exception\PlayerDieException
     */
    public function open(): static
    { 
        if ($this->isBomb()) {
            if ($this->flag) {
                return $this;
            }
            throw new PlayerDieException("Сдох");
        }
        $this->open = true;
        return $this;
    }

    public function isBomb(): bool
    {
        return false;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}