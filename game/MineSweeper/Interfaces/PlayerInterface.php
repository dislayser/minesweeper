<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface PlayerInterface
{
    public function getId(): int|string;
    
    public function getLive(): LiveInterface;
    
    public function win(): static;
    
    public function die(): static;
    
    public function isDie(): bool;
    
    public function isWin(): bool;
}