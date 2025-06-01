<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

use Game\Service\Util\StringUtil;

class CreateTable
{
    public string $name;

    /** @var Column[] $columns */
    public array  $columns = [];
    
    /**
     * Summary of __construct
     * @param string $name
     * @param Column[] $columns
     */
    public function __construct(
        string $name,
        array  $columns = []
    ) {
        $this->name = StringUtil::toSnakeCase($name);
        $this->columns = $columns;
    }

    /**
     * Summary of columns
     * @param Column[] $columns
     * @return CreateTable
     */
    public function columns(array $columns = []): static
    {
        $columns = array_values($columns);
        $this->columns = [...$this->columns, ...$columns];
        return $this;
    }

    public function build(): string
    {
        $columns = "";
        $sql = <<<SQL
        CREATE TABLE {$this->name} (
            {$columns}
        )
        SQL;

        return $sql;
    }

    public function __toString(): string
    {
        return $this->build();
    }
}