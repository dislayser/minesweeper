<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Player;
use Game\Service\Util\JsonUtil;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

// Create a Websocket server
$ws = new Worker('websocket://0.0.0.0:8080');

// Глобальное хранилище клиентов
class ClientStorage {
    public static $clients = [];
}

// Задаем количество процессов
$ws->count = 4;

// Emitted when new connection come
$ws->onConnect = function ($conn){
    ClientStorage::$clients[$conn->id] = new Game(
        new Player($conn->id),
        new Field(32, 16)
    );
    
    ClientStorage::$clients[$conn->id]->setDifficult(Difficult::easy());
    ClientStorage::$clients[$conn->id]->buildField();
    
    $conn->send(JsonUtil::stringify([
        "type" => "create",
        "cols" => ClientStorage::$clients[$conn->id]->field()->getX(),
        "rows" => ClientStorage::$clients[$conn->id]->field()->getY()
    ]));
    // dump(array_keys(ClientStorage::$clients));
};

// Emitted when data received
$ws->onMessage = function ($conn, $data) {
    $conn->send($data);
};

// Emitted when connection closed
$ws->onClose = function ($conn) {
    if (isset(ClientStorage::$clients[$conn->id])) {
        unset(ClientStorage::$clients[$conn->id]);
    }
    echo "Connection closed\n";
};

// Run worker
Worker::runAll();