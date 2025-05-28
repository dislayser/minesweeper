<?php

namespace Game\MineSweeper;

use Game\MineSweeper\CellType\Bomb;
use Game\MineSweeper\CellType\Number;

/**
 * -1   :: Bomb
 * 0-8  :: Number cell
 */
class Field
{
    public const MAX_X = 200;
    public const MAX_Y = 200;
    public const MIN_X = 1;
    public const MIN_Y = 1;
    /** @var array[Cell[]] $cells */
    private array $cells = [];
    private int $seed;
    private int $bombs;

    public function __construct(
        private int $x,
        private int $y,
        ?int $seed = null
    ) {
        $this->x = min(max($this->x, self::MIN_X), self::MAX_X);
        $this->y = min(max($this->y, self::MIN_Y), self::MAX_Y);
        // TODO: С веб сокета генерирует один и тот же сид
        if (!$seed) $seed = rand(1, 900000000);
        $this->seed = $seed;
    }

    public function getSeed() : int
    {
        return $this->seed;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function setCell(?Cell $cell): bool
    {
        if (!$cell) return false;
        $this->cells[$cell->getY()][$cell->getX()] = $cell;
        return true;
    }

    public function getCell(int $x, int $y): ?Cell
    {
        if (isset($this->cells[$y], $this->cells[$y][$x])){
            return $this->cells[$y][$x];
        }else{
            return null;
        }
    }

    /** @return (Cell|null)[] */
    public function getCellsNear(int $x, int $y): ?array
    {
        return [
            $this->getCell($x-1, $y-1),
            $this->getCell($x,   $y-1),
            $this->getCell($x+1, $y-1),
            $this->getCell($x-1, $y),
            // $this->getCell($x, $y),
            $this->getCell($x+1, $y),
            $this->getCell($x-1, $y+1),
            $this->getCell($x,   $y+1),
            $this->getCell($x+1, $y+1),
        ];
    }

    public function setBombs(int $bombs): void
    {
        $this->bombs = min($bombs, $this->getX() * $this->getY());
        mt_srand($this->seed);
        // Список всех возможных позиций
        $positions = [];
        for ($r = 0; $r < $this->getY(); $r++) {
            for ($c = 0; $c < $this->getX(); $c++) {
                $positions[] = [$c, $r];
            }
        }
        // Перемешиваем позиции
        shuffle($positions);

        // Установка бомб
        for ($i = 0; $i < $this->bombs; $i++){
            [$x, $y] = $positions[$i];
            $this->setCell(new Bomb($x, $y));
        }
        
        mt_srand(0);
    }

    public function getCells() : array
    {
        return $this->cells;
    }

    /** @return array[Cell[]] */
    public function openField(int $x, int $y) : array
    {
        $cells = []; 
        /** @var Bomb|Number|null $open */
        $open = $this->getCell($x,$y);
        $cells[$y][$x] = $open;
        if ($open?->isBomb() || $open?->getBombNear() > 0){
            return $cells;
        }
        return $cells;
    }
}