<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface ServerInterface
{
    public function getId(): int|string;
    
    public function getName(): string;
 
    /**
     * @return GameInterface[]
     */
    public function getGames(): array;

    /**
     * @return PlayerInterface[]
     */
    public function getPlayers(): array;

    public function getPlayerById(int|string $playerId): ?PlayerInterface;
    
    public function getGameByPlayer(PlayerInterface $player): ?GameInterface;

    public function addGame(GameInterface $game): static;
    
    public function getModerator(): PlayerInterface;
    
    public function setModerator(PlayerInterface $player): static;

    public function doAction(ActionInterface $action): mixed;
}
