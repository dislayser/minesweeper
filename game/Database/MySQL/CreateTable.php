<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

class CreateTable
{
    public function __construct(
        public string $name,
        public array  $columns = []
    ) {}

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