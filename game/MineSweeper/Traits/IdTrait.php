<?php

declare(strict_types=1);

namespace Game\MineSweeper\Traits;

trait IdTrait
{
    private int|string $id;

    public function getId(): int|string
    {
        return $this->id;
    }

    public function setId(int|string $id): void
    {
        $this->id = $id;
    }
}