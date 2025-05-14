<?php

namespace Game\Kernel;

class Router
{
    /** @var Route[] $routes */
    private array $routes = []; 

    public function __construct() {}

    public function addRoute(Route $route) : void
    {
        $routes[] = $route;
    }

    public function start(string $url, string $method): void
    {

    }

    public function error(int $code) : void
    {

    }
}