<?php

namespace Game;

use Game\Controller\IndexController;
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
        $sess = new Session();
        $sess->start();

        $router = new Router();
        $router->addRoute(
            new Route("/", IndexController::class, "index", ["GET"])
        );

        $bag = (new InputBag())->server();
        $router->start(
            parse_url($bag->getStr("REQUEST_URI", "/"), PHP_URL_PATH),
            $bag->getStr("REQUEST_METHOD")
        );
    }
}