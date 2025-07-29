<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface FieldInterface
{
    public function setCell(CellInterface $cell): bool;

    public function getCols(): int;
    
    public function getRows(): int;
    
    public function hasCell(int $col, int $row): bool;
    
    public function getCell(int $col, int $row): ?CellInterface; 
    
    /**
     * @return CellInterface[]
     */
    public function getCells(array $range): array;
} 