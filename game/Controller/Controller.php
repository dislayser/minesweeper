<?php

namespace Game\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller implements ControllerInterface
{
    public function render($name = "", array $content = []) : void
    {
        $data = [];
        $data["content"] = $content;
        $data["user"] = [
            "theme" => "dark"
        ];

        $loader = new FilesystemLoader(__DIR__."/../../templates");
        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => __DIR__."/../../cache/twig",
        ]);
        $render = $twig->render($name, $data);
        echo $render;
    }
    public function json(array|string $data = []) : void
    {

    }
    public function image() : void
    {
        
    }
}