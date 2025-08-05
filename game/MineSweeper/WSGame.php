<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use DateTime;
use Game\MineSweeper\Interfaces\ServerInterface;
use Game\Service\Util\JsonUtil;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class WSGame
{
    /**
     * @var array<TcpConnection>
     */
    private static array $clients = [];
    private static array $players = [];

    /**
     * @var array<ServerInterface>
     */
    private static array $servers = [];
    private static int $serverCount = 0;

    private static Worker $ws;

    public static function init(): void
    {
        self::$ws = new Worker('websocket://0.0.0.0:8080');
    }

    public static function ws(): Worker
    {
        return self::$ws;
    }

    private static array $types = [
        "CREATE"        => "create",
        "ERROR"         => "error",
        "CREATEGAME"    => "create_game",
        "CREATESERVER"  => "CREATESERVER",
        "DELSERVER"     => "DELSERVER",
        "GETSERVERS"    => "GETSERVERS",
        "JOINSERVER"    => "JOINSERVER",
        "JOINGAME"      => "JOINGAME",
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

    public static function removeServer(int $id): void
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
            if ($server->getId() === $serverId) {
                return $server;
            }
        }
        return null;
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

    public static function addClient(TcpConnection $client): void
    {
        self::$clients[$client->id] = $client;
        self::$players[$client->id] = new Player($client->id, new Live());

        self::sendMessage($client, [
            "type" => "info",
            "msg" => "Connection success",
            "id" => $client->id, // Отправляем уникальный ID клиенту
        ]);

        self::updateClients();
    }

    public static function removeClient(TcpConnection $client): void
    {
        dump("Выкл: " . $client->id);
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
                "user_id" => $item->getModerator()->getId(),
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
                "name" => "Игрок {$item->id}",
                "id" => $item->id,
            ];
            $info .= "Игрок {$item->id} | ";
        }
        dump("Обновление " . (new DateTime())->format("d.m.Y H:i:s"));
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
        foreach (self::ws()->connections as $client) {
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
            self::removeServer((int) $data["id"]);
        }

    }
}