<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

class Column
{
    /**
     * Summary of __construct
     * @param string $name
     * @param string $type
     * @param ColumnType[] $options
     */
    public function __construct(
        public string $name,    
        public string $type,    
        public array  $options = [],    
    ) {
    }
}