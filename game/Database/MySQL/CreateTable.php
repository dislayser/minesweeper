<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

class CreateTable
{
    public array $columns = [];

    public function __construct() {}

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
        $sql = <<<SQL
        SQL;

        return $sql;
    }

    public function __toString(): string
    {
        return $this->build();
    }
}