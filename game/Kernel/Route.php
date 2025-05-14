<?php

namespace Game\Kernel;

class Route
{
    public function __construct(
        public string $url,
        public string $action,
        public string $name = "",
        public array  $methods = ["GET", "POST"],
        public array  $options = []
    ) {}

    public function run() : void
    {
        $class = $this->action;
        $parts = explode("::", $this->action, 2);
        $method = $parts[1] ?? "index";
        if (!class_exists($class)){
            throw new \InvalidArgumentException("Такого класса не существует: {$class}");
        }

        if (method_exists($class, $method)){
            (new $class)->$method();
            return;
        }
    }
}