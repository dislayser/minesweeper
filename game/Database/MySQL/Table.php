<?php

declare(strict_types=1);

namespace Game\Database\MySQL;

use Game\Database\QueryBuilder;
use Game\Service\Util\StringUtil;

class Table
{
    private string $table;

    public function __construct(
        string $table
    ) {
        $this->table = StringUtil::toSnakeCase($table);
    }
    
    public function getAll(): array
    {
        $qb = (new QueryBuilder())
            ->select(["table_name"])
            ->from("information_schema.tables")
            ->andWhere("table_schema = :table")
            ->addParam("table", $this->table)
        ;
        return [];
    }

    public function create(?string $table = null) : CreateTable
    {
        return new CreateTable($table ?? $this->table);
    }

    public function has(?string $table = null): bool
    {
        return in_array($table ?? $this->table, $this->getAll());
    }
}