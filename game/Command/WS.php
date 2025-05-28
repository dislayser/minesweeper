<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Game\Entity\Server as ServerEntity;
use Game\Entity\Game as GameEntity;
use Game\Entity\Difficult as DifficultEntity;
use Game\MineSweeper\CellType\Number;
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
$ws->count = 8;

// Emitted when new connection come
$ws->onConnect = function ($conn){
    $server = (new ServerEntity())->get(1);
    $game = (new GameEntity())->get($server["game_id"]);
    $difficult = (new DifficultEntity())->get($game["difficult_id"]);

    // Создание нового клиента
    ClientStorage::$clients[$conn->id] = new Game(
        new Player($conn->id),
        new Field(
            $game["cols"],
            $game["rows"]
        )
    );
    
    // Установка сложности
    ClientStorage::$clients[$conn->id]->setDifficult(new Difficult(
        $difficult["name"],
        $difficult["bombs_ratio"],
    ));

    // Постройка игры
    ClientStorage::$clients[$conn->id]->buildField();
    
    $conn->send(JsonUtil::stringify([
        "type" => "create",
        "cols" => ClientStorage::$clients[$conn->id]->field()->getX(),
        "rows" => ClientStorage::$clients[$conn->id]->field()->getY(),
        "seed" => ClientStorage::$clients[$conn->id]->field()->getSeed(),
    ]));
};

// Emitted when data received
$ws->onMessage = function ($conn, $data) {
    // $conn->send($data);
    if (!isset(ClientStorage::$clients[$conn->id])) return;

    // Проверка выигрыша
    if (ClientStorage::$clients[$conn->id]->player()->isWin()) {
        $conn->send(JsonUtil::stringify([
            "type" => "win"
        ]));
        return;
    }

    // Проверка проигрыша
    if (ClientStorage::$clients[$conn->id]->player()->isDie()) {
        $conn->send(JsonUtil::stringify([
            "type" => "die"
        ]));
        return;
    }

    // Обработка сообщений
    $data = JsonUtil::parse($data);
    if (!isset($data["type"])) return;
    if ($data["type"] === "open") {
        $open = ClientStorage::$clients[$conn->id]->openCell(
            (int) $data["x"],
            (int) $data["y"]
        );

        if ($open) {
            $conn->send(JsonUtil::stringify([
                "type" => "cell",
                "x" => $open->getX(),
                "y" => $open->getY(),
                "isBomb" => $open->isBomb(),
                "number" => ($open instanceof Number ? $open->getBombNear() : null),
            ]));
        }
    }
    return;
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