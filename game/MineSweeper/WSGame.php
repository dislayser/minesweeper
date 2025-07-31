<?php

declare(strict_types=1);

namespace Game\MineSweeper;

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
     * @var array<ServerInterface>
     */
    private static array $servers = [];


    // @SERVERS:

    public static function addServer(ServerInterface $server): void
    {
        self::$servers[] = $server;
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
        self::sendMessage($client, [
            "type" => "info",
            "msg" => "Connection success",
        ]);
        dump(array_keys(self::$clients));
    }

    public static function removeClient(TcpConnection $client): void
    {
        if (isset(self::$clients[$client->id])) {
            unset(self::$clients[$client->id]);
        }
    }

    // @WS
    
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

    }
}