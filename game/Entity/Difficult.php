<?php

declare(strict_types=1);

namespace Game\Entity;

class Difficult extends Entity
{
    public function __construct() {
        parent::__construct("difficults");
    }

    public function get(int $id): array
    {
        return [
            "id" => 1,
            "name" => "easy",
            "bombs_ratio" => 10,
        ];
    }

    public function getByName(string $name): array
    {
        return $this->get(1);
    }
}