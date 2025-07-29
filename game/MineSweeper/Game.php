<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\Exception\GameException;
use Game\Exception\PlayerDieException;

class Game implements Interfaces\GameInterface
{
    use Traits\IdTrait;

    public const TYPE_MP = "MP";
    public const TYPE_SP = "SP";
    public const MIN_PLAYERS = 1;
    public const MAX_PLAYERS = 4;
    
    /**
     * @var Interfaces\PlayerInterface[]
     */
    private array $players = [];

    public function __construct(
        private string $type,
        private Interfaces\FieldInterface $field
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function setFlag(int $col, int $row, Interfaces\PlayerInterface $player): bool
    {
        $cell = $this->field->getCell($col, $row);
        try {
            if ($cell === null) return false;
            if ($cell->isOpen()) return false;

            $cell->setFlag();
            return true;
        } catch (PlayerDieException $e) {
            $player->die();
        }
        return false;
    }

    public function openCell(int $col, int $row, Interfaces\PlayerInterface $player): array
    {
        $cell = $this->field->getCell($col, $row);
        $cells = [];
        try {
            if ($cell === null) return $cells;
            if ($cell->isOpen()) return $cells;

            $cell->open();
            $cells[] = $cell;

            if (!$cell->isBomb() && $cell->getNumber() === 0) {
                $near = $this->field->getCellsNear($col, $row);
                foreach ($near as $nearCell) {
                    if ($nearCell === null) continue;
                    $opened = $this->openCell($nearCell->getCol(), $nearCell->getRow(), $player);
                    foreach ($opened as $openedCell) {
                        if (!in_array($openedCell, $cells)) {
                            $cells[] = $openedCell;
                        }
                    }
                }
            }
        } catch (PlayerDieException $e) {
            $player->die();
        }
        
        return $cells;
    }

    /**
     * @return array<Interfaces\CellInterface>
     */
    public function openCells(array $range, Interfaces\PlayerInterface $player): array 
    {
        $cells = [];
        foreach ($range as $colrow) {
            $opened = $this->openCell(
                $colrow[0],
                $colrow[1],
                $player
            );
            foreach ($opened as $cell) {
                if (!in_array($cell, $cells)) {
                    $cells[] = $cell;
                }
            }

            if ($player->isDie()) break;
        }
        return $cells;
    }

    public function addPlayer(Interfaces\PlayerInterface $player): static
    {
        if (!in_array($player, $this->players)) {
            if (count($this->players) >= self::MAX_PLAYERS) {
                throw new GameException("Больше ".self::MAX_PLAYERS." игроков в игре не может быть");
            }
            $this->players[] = $player;
        }
        return $this;
    }

    public function getPlayers(): array
    {
        if (count($this->players) < self::MIN_PLAYERS) {
            throw new GameException("Меньше ".self::MIN_PLAYERS." игрока в игре не может быть");
        }
        return $this->players;
    }

    public function isRuning(): bool
    {
        $count = 0;
        foreach ($this->players as $player) {
            if ($player->isDie() || $player->isWin()) {
                $count++;
            }
        }
        return count($this->players) === $count;
    }
}