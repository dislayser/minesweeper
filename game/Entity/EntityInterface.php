<?php

declare(strict_types=1);

namespace Game\Entity;

interface EntityInterface
{
    public function get(int $id) : ?array;
    public function new(array $data) : ?int;
    public function update(int $id, array $data) : ?array;
    public function delete(int $id) : ?array;
}