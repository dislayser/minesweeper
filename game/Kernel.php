<?php

namespace Game;

use Dotenv\Dotenv;
use Game\Controller;
use Game\Kernel\InputBag;
use Game\Kernel\Route;
use Game\Kernel\Router;
use Game\Kernel\Session;

class Kernel
{
    public const DEBUG = true;
    public const USE_CACHE = true;

    public function run() : void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();

        $sess = new Session();
        $sess->start();

        $router = new Router();
        $router
            ->addRoute(new Route(
                "/",
                Controller\IndexController::class,
                "index",
                ["GET"]
            ))
            ->addRoute(new Route(
                "/test",
                Controller\TestController::class,
                "test",
                ["GET"]
            ))
        ;

        $bag = (new InputBag())->server();
        $router->start(
            parse_url($bag->getStr("REQUEST_URI", "/"), PHP_URL_PATH),
            $bag->getStr("REQUEST_METHOD")
        );
    }
}