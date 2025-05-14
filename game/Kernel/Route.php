<?php

namespace Game\Kernel;

class Route
{
    public function __construct(
        public string $url,
        public string|object $action,
        public string $name = "",
        public array  $methods = ["GET", "POST"],
        public array  $options = []
    ) {}

    public function run() : void
    {
        
    }
}