<?php

namespace Game\Kernel;

class Router
{
    /** @var Route[] $routes */
    private array $routes = []; 

    public function __construct() {}

    public function addRoute(Route $route) : void
    {
        $this->routes[] = $route;
    }

    public function start(string $url, string $method): void
    {
        foreach ($this->routes as $route) {
            if ($route->url == $url && in_array($method, $route->methods)){
                $route->run();
            }
        }
    }

    public function error(int $code) : void
    {

    }
}