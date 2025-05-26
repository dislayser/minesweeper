<?php

declare(strict_types=1);

namespace Game\Controller;

use Game\Exeption\GameExeption;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Player;

class GameApiController extends Controller
{
    public function index() : void
    {
        $game = new Game(
            new Player(),
            new Field(
                16,
                32,
                $this->bag->query()->getInt("seed")
            )
        );

        try {
            $this->json([]);
        } catch (GameExeption $e) {
            $this->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
}