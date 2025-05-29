<?php

declare(strict_types=1);

namespace Game\Entity;

class Game extends Entity
{
    public function __construct() {
        parent::__construct("games");
    }

    public function get(int $id): array
    {
        return [
            "id" => 1,
            "rows" => 10,
            "cols" => 10,
            "seed" => null,
            "difficult_id" => 1,
        ];
    }
}