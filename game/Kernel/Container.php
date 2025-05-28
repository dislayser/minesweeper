<?php

declare(strict_types=1);

namespace Game\Kernel;

use Game\Exeption\GameExeption;

class Container implements ContainerInterface
{
    /** @var object[] $containers */
    protected array $containers = [];

    public function __construct() {}

    public function register(int|string $name, object $object): void
    {
        $this->containers[$name] = $object;
    }
    
    /**
     * @param int|string $name
     * @throws \Game\Exeption\GameExeption
     * @return object
     */
    public function get(int|string $name): object
    {
        if (isset($this->containers[$name])) {
            return $this->containers[$name];
        }else{
            throw new GameExeption("Container not found: " . $name, 1);
        }
    }
}