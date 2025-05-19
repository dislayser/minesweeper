<?php

namespace Game\Controller;

use Game\Service\Util\JsonUtil;
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
        dump($data);
        $render = $twig->render($name, $data);
        echo $render;
    }
    public function json(array|string $data = []) : void
    {
        if (is_array($data)){
            echo JsonUtil::parse($data);
        }
        if (is_string($data) && JsonUtil::isJson($data)){
            echo $data;
        }
        return;
    }
    public function image() : void
    {
        
    }
}