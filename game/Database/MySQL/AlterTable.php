<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

use Game\Service\Util\StringUtil;

class AlterTable
{
    public string $name;

    public function __construct(
        string $name,
        array  $columns = []
    ) {
        $this->name = StringUtil::toSnakeCase($name);
        $this->columns = $columns;
    }
}