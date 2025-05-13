<?php

namespace Game\MineSweeper;

use Game\MineSweeper\CellType\Bomb;

/**
 * -1   :: Bomb
 * 0    :: Empty cell
 * 1-8  :: Number cell
 */
class Field
{
    public const MAX_X = 200;
    public const MAX_Y = 200;
    public const MIN_X = 1;
    public const MIN_Y = 1;
    /**
     * @var array[Cell[]]
     */
    private array $cells = [];
    private int $seed;

    public function __construct(
        private int $x,
        private int $y,
        ?int $seed = null
    ) {
        $this->x = min(max($this->x, self::MIN_X), self::MAX_X);
        $this->y = min(max($this->y, self::MIN_Y), self::MAX_Y);
        if (!$seed){
            $seed = rand(1, 100000);
        }
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

    public function setBombs(int $bombs): void
    {
        mt_srand($this->seed);
        // Список всех возможных позиций
        $positions = [];
        for ($r = 0; $r < $this->getY(); $r++) {
            for ($c = 0; $c < $this->getX(); $c++) {
                $positions[] = [$r, $c];
            }
        }
        // Перемешиваем позиции
        shuffle($positions);

        // Установка бомб
        for ($i = 0; $i < $bombs; $i++){
            [$x, $y] = $positions[$i];
            $this->setCell(new Bomb($x, $y));
        }
        
        mt_srand(null);
    }
}