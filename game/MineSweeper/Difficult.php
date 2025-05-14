<?php

namespace Game\MineSweeper;

class Difficult
{
    /**
     * @param string $name some name
     * @param int $bombRatio = ~(total_cells / total_bombs)
     */
    public function __construct(
        private string $name,
        private int $bombRatio
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getBombRatio(): int
    {
        return $this->bombRatio;
    }

    public static function easy() : self
    {
        return new self(
            "easy",
            10
        );
    }

    public static function medium() : self
    {
        return new self(
            "medium",
            7
        );
    }

    public static function hard() : self
    {
        return new self(
            "hard",
            4
            
        );
    }
}