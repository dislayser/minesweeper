<?php

declare(strict_types=1);

namespace Game\Database;

use PDOStatement;

interface ConnectionInterface
{
    /**
     * @throws \Game\Exception\DatabaseException
     * @return void
     */
    public function connect(): void;

    public function isConnect(): bool;

    public function query(string $sql, array $params): PDOStatement;

    public function fetch(QueryBuilder $queryBuilder): array|bool;

    public function close(): void;
}