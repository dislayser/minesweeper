<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

use Game\Service\Util\StringUtil;

class Column
{
    public string $name;
    public ColumnType $type;
    public ColumnMod|array $options = [];  
    /**
     * Summary of __construct
     * @param string $name
     * @param ColumnType $type
     * @param ColumnMod[] $options
     */
    public function __construct(
        string $name,
        ColumnType $type,
        ColumnMod|array $options = [],    
    ) {
        if (!is_array($options)) $options = [$options];
        $this->name = StringUtil::toSnakeCase($name);
        $this->type = $type;
        $this->options = $options;
    }

    public function getSql(): string
    {
        $sql = <<<SQL
        
        SQL;
        return $sql;
    }
}