<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

// Create a Websocket server
$ws = new Worker('websocket://0.0.0.0:8080');
$clients = [];
// Задаем количество процессов
$ws->count = 4;

// Emitted when new connection come
$ws->onConnect = function ($connection) {
    $clients[$connection->id] = $connection->id;
    echo "New connection\n";
};

// Emitted when data received
$ws->onMessage = function ($connection, $data) {
    // Send hello $data
    $connection->send('Hello ' . $data);
};

// Emitted when connection closed
$ws->onClose = function ($connection) {
    echo "Connection closed\n";
};

// Run worker
Worker::runAll();