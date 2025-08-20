<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\MineSweeper\Interfaces\GameInterface;
use Game\MineSweeper\Interfaces\PlayerInterface;
use Game\MineSweeper\Interfaces\ServerInterface;
use Game\Service\Util\JsonUtil;
use Workerman\Connection\TcpConnection;

class WSGame
{
    /**
     * @var array<TcpConnection>
     */
    private static array $clients = [];

    /**
     * @var array<PlayerInterface>
     */
    private static array $players = [];

    /**
     * @var array<ServerInterface>
     */
    private static array $servers = [];
    private static int $serverCount = 0;
    private static int $gameCount = 0;

    public static $difficults = [
        "easy" => 10,
        "medium" => 7,
        "hard" => 4,
    ];

    private static array $types = [
        "CREATE"        => "create",
        "ERROR"         => "error",

        "SETPLAYERNAME" => "SETPLAYERNAME",
        
        "CREATEGAME"    => "CREATEGAME",
        "GETGAMES"      => "GETGAMES",
        "DELGAME"       => "DELGAME",
        "JOINGAME"      => "JOINGAME",

        "GETSERVERS"    => "GETSERVERS",
        "CREATESERVER"  => "CREATESERVER",
        "DELSERVER"     => "DELSERVER",
        "JOINSERVER"    => "JOINSERVER",

        "OPEN_CELL"     => "OPENCELL",
        "OPEN_CELLS"    => "OPENCELLS",
        "SET_FLAG"      => "SETFLAG",
    ];


    // @SERVERS:

    public static function addServer(ServerInterface $server): void
    {
        self::$servers[] = $server;

        self::updateServers();
    }

    // ToDo: Возможно стоить разрешить только для модера удалять сервер 
    public static function removeServer(int $id, string|int $clientId): void
    {
        foreach (self::$servers as $key => $server) {
            if ($server->getId() == $id) {
                unset(self::$servers[$key]);
                self::updateServers();
            }
        }
    }

    public static function getServer(int $index): ?ServerInterface
    {
        $server = self::$servers[$index] ?? null;
        return $server;
    }

    public static function getServerById(int|string $serverId): ?ServerInterface
    {
        foreach (self::$servers as $server) {
            if ($server->getId() == $serverId) {
                return $server;
            }
        }
        return null;
    }

    public static function addGame(int|string $serverId, GameInterface $game) {
        self::getServerById($serverId)?->addGame($game);
    }

    public static function updateGames(string|int $serverId): void
    {
        $server = self::getServerById($serverId);
        if ($server !== null) {
            $games = $server->getGames();
            $list = [];
            foreach ($games as $game) {
                $list[] = [
                    "server" => $server->getName(),
                    "id" => $game->getId(),
                    "name" => $game->getName(),
                ];
            }
            self::sendAll([
                "type" => "GETGAMES",
                "data" => $list,
            ]);
        }
    }


    // @CLIENTS:

    public static function getClient(int|string $clientId): ?TcpConnection
    {
        return self::$clients[$clientId] ?? null;
    }

    /**
     * @var array<TcpConnection>
     */
    public static function getClients(array $clientIds): array
    {
        $clients = [];
        foreach ($clientIds as $clientId) {
            $client = self::getClient($clientId);
            if ($client !== null) {
                $clients[$client->id] = $client;
            }
        }
        return $clients;
    }

    public static function getPlayerById(string|int $clientId): ?PlayerInterface
    {
        foreach (self::$players as $player) {
            if ($player->getId() == $clientId) {
                return $player;
            }
        }
        return null;
    }

    public static function setPlayerName(string|int $clientId, string $name): void
    {
        $player = self::getPlayerById($clientId);
        $player->setName($name);
        self::updateClients();
    }

    public static function addClient(TcpConnection $client): void
    {
        self::$clients[$client->id] = $client;

        self::$players[$client->id] = new Player($client->id, new Live());
        self::$players[$client->id]->setName("Игрок {$client->id}");
        
        dump("=== CONNECT === client_id: {$client->id}");

        self::sendMessage($client, [
            "type" => "info",
            "msg" => "Connection success",
            "id" => $client->id, // Отправляем уникальный ID клиенту
            "name" => self::$players[$client->id]->getName(),
        ]);

        self::updateClients();
    }

    public static function removeClient(TcpConnection $client): void
    {
        dump("Выкл: {$client->id}");
        self::updateClients();
        if (isset(self::$clients[$client->id])) {
            unset(self::$clients[$client->id]);
            self::updateClients();
        }
    }

    // @WS

    public static function updateServers(): void
    {
        $list = [];
        $info = "";
        foreach (self::$servers as $item) {
            $list[] = [
                "name" => $item->getName(),
                "id" => $item->getId(),
                "user_id" => $item->getModerator()->getName(),
            ];
            $info .= "{$item->getName()} | ";
        }
        dump($info);
        self::sendAll([
            "type" => "GETSERVERS",
            "data" => $list,
        ]);
    }


    public static function updateClients(): void
    {
        $list = [];
        $info = "";
        foreach (self::$clients as $item) {
            $list[] = [
                "name" => self::getPlayerById($item->id)->getName(),
                "id" => $item->id,
            ];
            $info .= self::getPlayerById($item->id)->getName() . " | ";
        }
        dump("Обновление " . (new \DateTime())->format("d.m.Y H:i:s"));
        dump($info);
        self::sendAll([
            "type" => "new_player",
            "data" => $list,
        ]);
    }

    public static function sendAll(array|string $message): void
    {
        if (is_array($message)) {
            $message = JsonUtil::stringify($message);
        }
        foreach (self::$clients as $client) {
            $client->send($message);
        } ;
    }
    
    /**
     * @param TcpConnection|array<TcpConnection> $clients
     */
    public static function sendMessage(TcpConnection|array $clients, string|array $message): void
    {
        if (!is_array($clients)) {
            $clients = [$clients];
        }
        if (is_array($message)) {
            $message = JsonUtil::stringify($message);
        }
        foreach ($clients as $client) {
            $client->send($message);
        }
    }

    public static function onMessage(TcpConnection $client, string $message): void
    {
        /**
         * @var array{type: string}
         */
        $data = JsonUtil::parse($message);
        
        $type = $data["type"] ?? "null";

        dump("=== TYPE === " . $type);
        
        if ($type === self::$types["CREATESERVER"]) {
            self::$serverCount++;
            $server = new Server();
            $server->setId(self::$serverCount);
            $server->setName("Сервер {$server->getId()}");
            $server->setModerator(self::$players[$client->id] ?? new Player($client->id, new Live()));
            self::addServer($server);
        }

        if ($type === self::$types["GETSERVERS"]) {
            self::updateServers();
        }

        if ($type === self::$types["DELSERVER"]) {
            self::removeServer((int) $data["serverId"], $client->id);
        }

        if ($type === self::$types["JOINSERVER"]) {
            $player = self::getPlayerById($client->id);
            if (isset($data["serverId"]) && $player !== null) {
                $server = self::getServerById($data["serverId"]);

                self::sendMessage($client, [
                    "type" => $type,
                    "id" => $server->getId()
                ]);
            }
        }

        if ($type === self::$types["GETGAMES"]) {
            if (isset($data["serverId"])) {
                $server = self::getServerById($data["serverId"]);
                if ($server === null) {
                    self::updateServers();
                } else {
                    $games = $server->getGames();
                    $list = [];
                    foreach ($games as $game) {
                        $list[] = [
                            "id" => $game->getId(),
                            "server" => $server->getName(),
                            "name" => $game->getName(),
                        ];
                    }
                    self::sendMessage($client, [
                        "type" => $type,
                        "data" => $list
                    ]);
                }
            }
        }
        if ($type === self::$types["CREATEGAME"]) {
            $server = self::getServerById($data["serverId"]);
            if ($server === null) {
                self::updateServers();
            } else {
                $game = new Game(Game::TYPE_MP, new Field(
                    $cols = (int) $data["cols"],
                    $rows = (int) $data["rows"],
                    $bomb = (int) ($rows * $rows / self::$difficults[$data["difficult"]]),
                    $seed = (int) $data["seed"],
                ));
                self::$gameCount++;
                $game->setId(self::$gameCount);
                $game->setName("Игра " . self::$gameCount);
                self::addGame($server->getId(), $game);
                self::updateGames($server->getId());
            }
        }
        if ($type === self::$types["SETPLAYERNAME"]) {
            self::setPlayerName($client->id, (string) $data["name"]);
        }

    }
}