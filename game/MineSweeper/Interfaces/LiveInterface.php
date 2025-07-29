<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface LiveInterface
{
    public function decrement(): static;
    
    public function isDie(): bool;
}