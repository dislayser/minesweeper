<?php

declare(strict_types=1);

namespace Game\MineSweeper;

use Game\Exception\GameException;

class Server implements Interfaces\ServerInterface
{
    use Traits\IdTrait;

    public const MIN_GAMES = 1;
    public const MAX_GAMES = 4;
    private array $games = [];
    private Interfaces\PlayerInterface $moderator;
    
    public function __construct() {}

    public function addGame(Interfaces\GameInterface $game): static
    {
        if (!in_array($game, $this->games)) {
            if (count($this->games) >= self::MAX_GAMES) {
                throw new GameException("Больше 4 игр в сервере не может быть");
            }
            $this->games[] = $game;
        }
        return $this;
    }

    public function getGames(): array
    {
        if (count($this->games) < self::MIN_GAMES) {
            throw new GameException("Меньше 1 игры в сервере не может быть");
        }
        return $this->games;
    }

    public function getPlayers(): array
    {
        $players = [];
        foreach ($this->getGames() as $game) {
            $players = [
                ...$players,
                ...$game->getPlayers()
            ];
        }
        return $players;
    }

    public function getPlayerById(int|string $playerId): ?Interfaces\PlayerInterface
    {
        $player = null;
        foreach ($this->getPlayers() as $p) {
            if ($p->getId() === $playerId) {
                $player = $p;
            }
        }
        return $player;
    }

    public function getGameByPlayer(Interfaces\PlayerInterface $player): ?Interfaces\GameInterface
    {
        foreach ($this->getGames() as $game) {
            if (in_array($player, $game->getPlayers())) {
                return $game;
            }
        }
        return null;
    }

    public function setModerator(Interfaces\PlayerInterface $player): static
    {
        $this->moderator = $player;
        return $this;
    }

    // TODO: Тут нужен логгер - для логирования действий юзера
    public function doAction(Interfaces\ActionInterface $action): mixed
    {
        $player = null;
        foreach ($this->getPlayers() as $p) {
            if ($p->getId() === $action->getPlayerId()) {
                $player = $p;
            }
        }
        
        if ($player === null) return null;

        if ($action->getType() === Action::TYPE_OPENCELL) {
            return $this
                ->getGameByPlayer($player)
                ->openCell(
                    $action->getCellData()["col"],
                    $action->getCellData()["row"],
                    $player
                );
        }

        if ($action->getType() === Action::TYPE_SETFLAG) {
            return $this
                ->getGameByPlayer($player)
                ->setFlag(
                    $action->getCellData()["col"],
                    $action->getCellData()["row"],
                    $player
                );
        }

        if ($action->getType() === Action::TYPE_OPENCELLS) {
            return $this
                ->getGameByPlayer($player)
                ->openCells(
                    $action->getCellsData(),
                    $player
                );
        }
        
        return null;
    }
}