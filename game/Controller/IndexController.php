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
        $this->render("game/index.twig", []);
    }
}