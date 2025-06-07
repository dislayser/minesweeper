<?php

declare(strict_types=1);

namespace Game\Database;

use Game\Exception\DatabaseException;
use PDO;
use PDOException;
use PDOStatement;

class Connection implements ConnectionInterface
{
    private ?PDO $pdo = null;

    public function __construct(
        private string $dsn,
        private string $username,
        private string $password,
        private array $options = []
    ) {}

    public function connect(): void
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
            } catch (PDOException $e) {
                throw new DatabaseException('Connection failed: ' . $e->getMessage());
            }
        }
    }

    public function isConnect(): bool
    {
        return $this->pdo !== null;
    }

    public function query(string $sql, array $params): PDOStatement
    {
        $this->connect();
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new DatabaseException('Query failed: ' . $e->getMessage());
        }
    }

    public function fetch(QueryBuilder $queryBuilder): array|bool
    {
        $stmt = $this->query($queryBuilder->build(), $queryBuilder->getParams());
        return $stmt->fetchAll();
    }

    public function close(): void
    {
        $this->pdo = null;
    }
}