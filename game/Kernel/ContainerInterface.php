<?php

declare(strict_types=1);

namespace Game\Kernel;

interface ContainerInterface
{
    public function register(int|string $name, object $object): void;
    public function get(int|string $name): object;
}