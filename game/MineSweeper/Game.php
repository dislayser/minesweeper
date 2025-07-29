<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\Exception\GameException;
use Game\MineSweeper\CellType\Bomb;
use Game\MineSweeper\CellType\Number;

class Game implements Interfaces\GameInterface
{
    use Traits\GetIdTrait;

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

    public function openCell(int $col, int $row): ?Interfaces\CellInterface
    {
        $cell = $this->field->getCell($col, $row);
        if ($cell && $cell->isBomb()) {
            $this->player->die();
        }
        return $cell;
    }

    public function openCells(array $range): array 
    {
        $cells = [];
        foreach ($range as $row => $cols) {
            foreach ($cols as $col => $click) {
                if ($click === "click") {
                    $cells[] = $this->openCell($col, $row);
                }
            }
        }
        return $cells;
    }

    public function field(): Field
    {
        return $this->field;
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