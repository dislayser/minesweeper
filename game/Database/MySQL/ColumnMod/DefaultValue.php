<?php

declare(strict_types=1);

namespace Game\Database\MySQL\ColumnMod;

use Game\Database\MySQL\ColumnMod;

class DefaultValue extends ColumnMod
{
    public function __construct(
        public mixed $default
    ) {}
}