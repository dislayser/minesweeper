<?php

declare(strict_types=1);

namespace Game\Database\MySQL\ColumnType;

use Game\Database\MySQL\ColumnType;

class Def extends ColumnType
{
    public function __construct(
        public string $default
    ) {}
}