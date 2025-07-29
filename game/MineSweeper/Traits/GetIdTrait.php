<?php

declare(strict_types=1);

namespace Game\MineSweeper\Traits;

trait GetIdTrait
{
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }
}