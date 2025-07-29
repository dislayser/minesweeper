<?php

declare(strict_types=1);

namespace Game\MineSweeper;

class Live implements Interfaces\LiveInterface
{
    public const MIN_LIVES = 1;
    public const MAX_LIVES = 3;
    private int $lives;

    public function __construct(int $lives = 1) {
        $this->lives = max(
            min(
                $lives,
                self::MAX_LIVES
            ),
            self::MIN_LIVES
        );
    }

    public function decrement(): static
    {
        $this->lives--;
        return $this;    
    }
    
    public function isDie(): bool
    {
        return $this->lives === 0;
    }
}