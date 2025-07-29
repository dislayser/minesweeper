<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\MineSweeper\CellType\CellBomb;
use Game\MineSweeper\CellType\CellNumber;
use Game\MineSweeper\Interfaces\CellInterface;

class Field implements Interfaces\FieldInterface
{
    public const MAX_COL = 199;
    public const MAX_ROW = 199;
    public const MIN_COL = 0;
    public const MIN_ROW = 0;

    /**
     * @var array<array<Interfaces\CellInterface>> $cells
     */
    private array $cells = [];
    private int $cols;
    private int $rows;
    private int $bombs;
    private ?int $seed;

    public function __construct(
        int $cols,
        int $rows,
        int $boms,
        ?int $seed = null,
    ) {
        $this->cols = min(max($cols, self::MIN_COL), self::MAX_COL);
        $this->rows = min(max($rows, self::MIN_ROW), self::MAX_ROW);
        $this->setBombs($boms);
        $this->seed = (int) $seed; // $seed = rand(1, PHP_INT_MAX);

        $this->init();
    }

    private function init() : void
    {
        // Установка сида
        mt_srand($this->seed);
        
        // Список всех возможных позиций
        $positions = [];
        for ($r = 0; $r < $this->getRows(); $r++) {
            for ($c = 0; $c < $this->getCols(); $c++) {
                $positions[] = [$c, $r];
            }
        }

        // Перемешиваем позиции
        shuffle($positions);

        // Установка бомб
        for ($i = 0; $i < $this->bombs; $i++){
            [$x, $y] = $positions[$i];
            $this->setCell(new CellBomb($x, $y));
        }
        
        // Сброс сида
        mt_srand(0);

        // Заполнение остальных ячеек
        for ($r = 0; $r < $this->getRows(); $r++) {
            for ($c = 0; $c < $this->getCols(); $c++) {
                if (!$this->hasCell($c, $r)) {
                    $new = new CellNumber($c, $r, $this->getBombsNear($c, $r));
                    $this->setCell($new);
                }
            }
        }
    }

    public function getBombsNear(int $col, int $row): int
    {
        $near = [
            $this->getCell($col - 1, $row + 1),
            $this->getCell($col,     $row + 1),
            $this->getCell($col + 1, $row + 1),
            $this->getCell($col - 1, $row),
            $this->getCell($col + 1, $row),
            $this->getCell($col - 1, $row - 1),
            $this->getCell($col,     $row - 1),
            $this->getCell($col + 1, $row - 1),
        ];

        $bombs = 0;
        foreach ($near as $cell) {
            if ($cell && $cell->isBomb()) {
                $bombs++;  
            }
        }

        return $bombs;
    }


    public function getSeed() : int
    {
        return $this->seed;
    }

    public function getCols() : int
    {
        return $this->cols;
    }

    public function getRows() : int
    {
        return $this->rows;
    }

    public function setCell(CellInterface $cell): bool
    {
        $col = $cell->getCol();
        $row = $cell->getRow();

        if (
            self::MIN_COL <= $col && $col <= self::MAX_COL &&
            self::MIN_ROW <= $row && $row <= self::MAX_ROW
        ) {
            $this->cells[$row][$col] = $cell;
            return true;
        }
        return false;
    }

    public function hasCell(int $col, int $row): bool
    {
        return isset(
            $this->cells[$row],
            $this->cells[$row][$col]
        );
    }

    public function getCell(int $col, int $row): ?Interfaces\CellInterface
    {
        if ($this->hasCell($col, $row)) {
            return $this->cells[$row][$col];
        }
        return null;
    }

    private function setBombs(int $bombs): void
    {
        $this->bombs = max(0, min(
            $this->getCols() * $this->getRows(),
            $bombs,
        ));
    }

    /**
     * @return array<array<Interfaces\CellInterface>>
     */
    public function getCells(array $range) : array
    {
        return $this->cells;
    }
}