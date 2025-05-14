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
            new Field(20,20)
        );
        $game->setDifficult(Difficult::hard());
        $game->buildField();

        $this->render("base.html.twig", [
            "field" => $game->field()
        ]);
    }
}