<?php

declare(strict_types=1);

namespace Game\MineSweeper\Interfaces;

interface ServerInterface
{
    public function getId(): string;
 
    /**
     * @return GameInterface[]
     */
    public function getGames(): array;

    /**
     * @return PlayerInterface[]
     */
    public function getPlayers(): array;

    public function getGameByPlayer(PlayerInterface $player): ?GameInterface;

    public function addGame(GameInterface $game): static;
    
    public function setModerator(PlayerInterface $player): static;

    //public function doAction(): static;
}
