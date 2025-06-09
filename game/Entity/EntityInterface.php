<?php

declare(strict_types=1);

namespace Game\Entity;

interface EntityInterface
{
    public function get(int $id) : ?array;
    public function add(array $data) : int;
    public function set(int $id, array $data) : ?array;
    public function del(int $id) : bool;
    public function remove(int $id) : bool;
}