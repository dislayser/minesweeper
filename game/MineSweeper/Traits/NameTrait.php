<?php

declare(strict_types=1);

namespace Game\MineSweeper\Traits;

trait NameTrait
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}