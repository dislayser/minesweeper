<?php

declare(strict_types=1);

namespace Game\Kernel;

use Game\Exeption\ContainerExeption;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var object[] $containers */
    protected array $containers = [];

    public function __construct() {}

    public function has(int|string $id) : bool
    {
        return isset($this->containers[$id]);
    }

    public function register(int|string $id, object $object): void
    {
        $this->containers[$id] = $object;
    }
    
    /**
     * @param int|string $name
     * @throws \Game\Exeption\ContainerExeption
     * @return object
     */
    public function get(int|string $id): object
    {
        if (isset($this->containers[$id])) {
            return $this->containers[$id];
        }else{
            throw new ContainerExeption("Container not found: " . $id, 1);
        }
    }
}