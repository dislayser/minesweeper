<?php
// TODO: WS нужно переписать
require_once __DIR__ . '/../../vendor/autoload.php';

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
    /**
     * @var Game[] $clients 
     */
    public static $clients = [];
}

// Задаем количество процессов
$ws->count = 8;

// Emitted when new connection come
$ws->onConnect = function ($conn){
    if (isset(ClientStorage::$clients[$conn->id])) return;

    ClientStorage::$clients[$conn->id] = new Game(
        new Player(),
        new Field(10, 10)
    );
};

// Emitted when data received
$ws->onMessage = function ($conn, $data) {
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

    if ($data["type"] === "create") {
        if (isset($data["cols"], $data["rows"], $data["seed"], $data["difficult"])) {
            dump($data);
            $rows = (int) $data["rows"];
            $cols = (int) $data["cols"];
            $seed = (int) $data["seed"];
            $difficult = (string) $data["difficult"];

            // Validation
            if ($rows < 2 || $cols < 2 || empty($seed) || !method_exists(Difficult::class, $difficult)) return;

            $client = &ClientStorage::$clients[$conn->id];

            // Создание нового клиента
            $client = new Game(
                new Player($conn->id),
                new Field($cols, $rows, $seed)
            );
            
            // Установка сложности
            $client->setDifficult(Difficult::$difficult());

            // Постройка игры
            $client->buildField();
            
            // Отправка клиенту
            $conn->send(JsonUtil::stringify([
                "type" => "create",
                "cols" => $client->field()->getX(),
                "rows" => $client->field()->getY(),
                "seed" => $client->field()->getSeed(),
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