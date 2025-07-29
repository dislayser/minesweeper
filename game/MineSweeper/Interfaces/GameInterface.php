<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface GameInterface
{
    public function getId(): string;
    /**
     * @return "MP"|"SP"
     */
    public function getType(): string;

    /**
     * @return PlayerInterface[]
     */
    public function getPlayers(): array;
    
    public function addPlayer(PlayerInterface $player): static;
    
    public function openCell(int $col,  int $row): ?CellInterface;

    /**
     * @return CellInterface[]
     */
    public function openCells(array $range): array;

    public function isRuning(): bool;
}