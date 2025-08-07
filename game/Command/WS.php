<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Game\MineSweeper\WSGame;
use Workerman\Worker;

$ws = new Worker('websocket://0.0.0.0:8080');

$ws->count = 1;
$ws->onConnect = [WSGame::class, "addClient"];
$ws->onMessage = [WSGame::class, "onMessage"];
$ws->onClose   = [WSGame::class, "removeClient"];

Worker::runAll();