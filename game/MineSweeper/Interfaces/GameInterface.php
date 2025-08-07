<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface GameInterface
{
    public function getId(): int|string;
    public function getName(): string;
    /**
     * @return "MP"|"SP"
     */
    public function getType(): string;

    public function getField(): FieldInterface;

    /**
     * @return PlayerInterface[]
     */
    public function getPlayers(): array;
    
    public function addPlayer(PlayerInterface $player): static;
    
    /**
     * @return CellInterface[]
     */
    public function openCell(int $col, int $row, PlayerInterface $player): array;
    
    public function setFlag(int $col,  int $row, PlayerInterface $player): bool;

    /**
     * @return CellInterface[]
     */
    public function openCells(array $range, PlayerInterface $player): array;

    public function isRuning(): bool;
}