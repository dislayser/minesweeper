<?php

namespace Game\Controller;

use Game\Kernel\InputBag;
use Game\Service\Util\JsonUtil;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller implements ControllerInterface
{
    readonly protected InputBag $bag;

    public function __construct()
    {
        $this->bag = new InputBag(); 
    }

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
        dump($data);
    }

    public function json(array|string $data = [], int $code = 200) : void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        if (is_array($data)){
            echo JsonUtil::stringify($data);
        }
        if (is_string($data) && JsonUtil::isJson($data)){
            echo $data;
        }
        return;
    }

    public function sse($data): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Пример: Отправка начального состояния игры
        echo "data: " . JsonUtil::stringify($data) . "\n\n";
        ob_flush();
        flush();
    }
    
    public function image() : void
    {
        
    }
}