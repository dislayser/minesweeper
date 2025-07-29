<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface CellInterface
{
    public function getCol(): int;
    
    public function getRow(): int;
    
    public function getNumber(): int;
    
    public function isBomb(): bool;
    
    public function isOpen(): bool;
    
    public function setFlag(): static;
    
    public function open(): static;
}