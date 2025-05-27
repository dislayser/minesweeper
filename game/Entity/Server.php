<?php

declare(strict_types=1);

namespace Game\Entity;

class Server extends Entity
{
    public function __construct() {
        parent::__construct("servers");
    }

    public function get(int $id): array
    {
        return [
            "id" => 1,
            "name" => "My server",
            "game_id" => 1,
        ];
    }
}