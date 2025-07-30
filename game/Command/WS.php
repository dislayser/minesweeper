<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Game\MineSweeper\Action;
use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Interfaces\ServerInterface;
use Game\MineSweeper\Live;
use Game\MineSweeper\Player;
use Game\MineSweeper\Server;
use Game\MineSweeper\WSGame;
use Game\Service\Util\JsonUtil;

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

// Create a Websocket server
$ws = new Worker('websocket://0.0.0.0:8080');

/**
 * Global storage
 */
class WSStorage
{
    private static $servers = [];

    public static function getActual(): ?ServerInterface
    {
        if (count(self::$servers) === 0) {
            return null;
        }
        return end(self::$servers)["server"];
    }

    public static function add(ServerInterface $server): void
    {
        self::$servers[] = [
            "time" => new \DateTime(),
            "server" => $server,
        ];
    }
}

// Задаем количество процессов
$ws->count = Server::MAX_GAMES * Game::MAX_PLAYERS;

// Emitted when new connection come
$ws->onConnect = [WSGame::class, "addClient"];
// $ws->onConnect = function ($conn){
//     WSGame::addClient($conn);
//     /**
//      * @var TcpConnection $conn
//      */
//     $conn->send(JsonUtil::stringify([
//         "type" => "info",
//         "msg" => "Connection success",
//     ]));
// };

// Emitted when data received
$ws->onMessage = [WSGame::class, "onMessage"];
$ws->onMessage = function ($conn, $json) {
    /**
     * @var TcpConnection $conn
     * @var string $json
     * @var array $data
     */
    $data = JsonUtil::parse($json);
    if (!isset($data["type"])) return;

    $server = WSStorage::getActual();

    if ($data["type"] === "create") {
        
        if (!$server) {
            $diff = [
                "easy" => 10,
                "medium" => 7,
                "hard" => 4,
            ];
            $server = new Server();
            $game = new Game(
                Game::TYPE_SP,
                new Field(
                    $rows = (int) $data["rows"],
                    $cols = (int) $data["cols"],
                    (int) ($rows * $cols / $diff[$data["difficult"]]),
                    $seed = (int) $data["seed"],
                )
            );
            $game->addPlayer(new Player($conn->id, new Live()));
            $server->addGame($game);
            $server->setModerator($player = new Player($conn->id, new Live()));
            WSStorage::add($server);
        
            // Отправка клиенту
            $conn->send(JsonUtil::stringify([
                "type" => "create",
                "cols" => $cols,
                "rows" => $rows,
                "seed" => $seed,
            ]));
        }
    }

    if ($data["type"] === Action::TYPE_OPENCELL) {
        /**
         * @var \Game\MineSweeper\Interfaces\CellInterface[] $opened
         */
        $opened = $server->doAction(new Action(
            $data["type"],
            $conn->id,
            ["col" => $data["col"], "row" => $data["row"]]
        ));

        $send = [];
        foreach ($opened as $open) {
            $send[] = [
                "col" => $open->getCol(),
                "row" => $open->getRow(),
                "isBomb" => $open->isBomb(),
                "number" => $open->getNumber(),
            ];
        }

        $conn->send(JsonUtil::stringify([
            "type" => $data["type"],
            "data" => $send
        ]));
    }

    return;
};

// Emitted when connection closed
$ws->onClose = [WSGame::class, "removeClient"];
$ws->onClose = function ($conn) {
    /**
     * @var TcpConnection $conn
     */
    echo "Connection closed :: ID:{$conn->id}\n";
};

// Run worker
Worker::runAll();