<?php

declare(strict_types=1);

namespace Game\Entity;

use Game\Database\QueryBuilder;

class Entity implements EntityInterface
{
    public function __construct(
        protected string $table
    ) {}

    public function get(int $id) : ?array
    {
        $qb = (new QueryBuilder())
            ->select(["e.*"])
            ->from($this->table, "e")
            ->andWhere("e.id = :id")
            ->addParam("id", $id)
            ->limit(1)
        ;

    }

    public function add(array $data) : int
    {

    }

    public function set(int $id, array $data) : ?array
    {

    }

    public function del(int $id) : bool
    {
        return false;
    }

    public function remove(int $id) : bool
    {
        return false;
    }
}