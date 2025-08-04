<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Game\MineSweeper\WSGame;
use Workerman\Worker;

WSGame::init();

WSGame::ws()->count = 1;
WSGame::ws()->onConnect = [WSGame::class, "addClient"];
WSGame::ws()->onMessage = [WSGame::class, "onMessage"];
WSGame::ws()->onClose = [WSGame::class, "removeClient"];

Worker::runAll();