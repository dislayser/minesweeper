<?php

declare(strict_types=1);

namespace Game\Kernel;

use Game\Exception\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var array<string, callable|object> */
    protected array $services = [];

    /**
     * Проверяет, существует ли сервис с указанным идентификатором.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * Регистрирует сервис в контейнере.
     *
     * @param string $id
     * @param callable|object $service
     */
    public function register(string $id, callable|object $service): static
    {
        $this->services[$id] = $service;
        return $this;
    }

    /**
     * Возвращает сервис по указанному идентификатору.
     *
     * @param int|string $id
     * @throws ContainerException Если сервис не найден.
     * @return object
     */
    public function get(string $id): object
    {
        if (!isset($this->services[$id])) {
            throw new ContainerException("Service not found: " . $id, 1);
        }

        $entry = $this->services[$id];
        return $entry instanceof \Closure ? $entry() : $entry;
    }

    private function resolve(): object
    {
    }
}