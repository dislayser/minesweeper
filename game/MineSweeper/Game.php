<?php

namespace Game\MineSweeper;

use Game\MineSweeper\CellType\Bomb;
use Game\MineSweeper\CellType\Number;

class Game
{
    private Difficult $difficult;

    public function __construct(
        private Player $player,
        private Field $field
    ) {}

    public function setDifficult(Difficult $difficult) : void
    {
        $this->difficult = $difficult;
    } 

    public function getDifficult() : Difficult
    {
        return $this->difficult;
    }

    public function buildField(): void
    {
        // Постройка бомб
        $this->field->setBombs((int)($this->field->getX() * $this->field->getY() / $this->difficult->getBombRatio()));

        // Постройка числовых ячеек
        for ($y = 0; $y < $this->field->getY(); $y++) {
            for ($x = 0; $x < $this->field->getX(); $x++) {
                $cell = $this->field->getCell($x,$y);
                if ($cell instanceof Bomb) continue;
                
                $cell = new Number($x, $y);
                
                $cellsNear = $this->field()->getCellsNear($x, $y);
                
                $bombs = 0;
                foreach ($cellsNear as $key => $value) {
                    if ($value?->isBomb()) $bombs++;
                }
                $cell->setBombNear($bombs);

                $this->field->setCell($cell);
            }
        }
    }

    public function openCell(int $x, int $y): ?Cell
    {
        $cell = $this->field->getCell($x, $y);
        if ($cell && $cell->isBomb()) {
            $this->player->die();
        }
        return $cell;
    }


    public function openCells(): array 
    {
        $cells = [];
        return $cells;
    }

    public function field(): Field
    {
        return $this->field;
    }

    public function player(): Player
    {
        return $this->player;
    }
}