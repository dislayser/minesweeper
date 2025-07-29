<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\Exception\GameException;
use Game\Exception\PlayerDieException;

class Player implements Interfaces\PlayerInterface
{
    use Traits\GetIdTrait;

    private bool $win = false;

    public function __construct(
        int|string $id,
        private Interfaces\LiveInterface $live
    ) {
        $this->id = $id;
    }

    public function getLive(): Interfaces\LiveInterface
    {
        return $this->live;
    }

    /**
     * @throws \Game\Exception\PlayerDieException Если игрок проиграл
     */
    public function win(): static
    {
        if ($this->isDie()) throw new PlayerDieException("Этот игрок проиграл.");
        
        $this->win = true;
        
        return $this;
    }

    /**
     * Вызывать метод при взрыве
     * @throws \Game\Exception\GameException Если игрок выиграл
     */
    public function die(): static
    {
        if ($this->isWin()) throw new GameException("Этот игрок выиграл.");
        
        $this->live->decrement();
        
        return $this;
    }

    public function isWin(): bool
    {
        return $this->win;
    }

    public function isDie(): bool
    {
        return $this->live->isDie();
    }
}