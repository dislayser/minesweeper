<?php

namespace Game\Controller;

use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Player;

class IndexController extends Controller
{
    public function index() : void
    {
        $game = new Game(
            new Player(),
            new Field(10,10)
        );
        $game->setDifficult(Difficult::hard());
        $game->buildField();

        $this->render("game/index.twig", [
            "field" => $game->field()
        ]);
    }
}