<?php

declare(strict_types=1);

namespace Game\Database;

use Game\Exception\DatabaseException;

class Connection
{
    public function __construct(
        private string $dsn
    ) {
    }


    public function connect(): void
    {
    }
}