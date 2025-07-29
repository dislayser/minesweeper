<?php

declare(strict_types=1);

namespace Game\MineSweeper\Traits;

trait GetIdTrait
{
    private int|string $id;

    public function getId(): int|string
    {
        return $this->id;
    }
}