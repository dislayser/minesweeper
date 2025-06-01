<?php

declare(strict_types=1);

namespace Game\Kernel;

class Router
{
    /** @var Route[] $routes */
    private array $routes = []; 

    public function __construct() {}

    public function addRoute(Route $route) : static
    {
        $this->routes[] = $route;
        return $this;
    }

    public function start(string $url, string $method): void
    {
        foreach ($this->routes as $route) {
            if ($route->url == $url && in_array($method, $route->methods)){
                $route->run();
            }
        }
        http_response_code(404);
    }

    public function error(int $code) : void
    {

    }
}