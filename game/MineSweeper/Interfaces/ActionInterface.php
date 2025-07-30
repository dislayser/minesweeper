<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface ActionInterface
{
    public function getOptions(): ?array;

    public function getPlayerId(): int|string;

    public function getType(): string;

    /**
     * @return array{col: int, row: int}
     */
    public function getCellData(): array;
    
    /**
     * @return array<array{0: int, 1: int}>
     */
    public function getCellsData(): array;
}