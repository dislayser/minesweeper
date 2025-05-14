<?php

use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Player;

require_once(dirname(__FILE__) . "../config/Config.php");
require_once(dirname(__FILE__) . "../vendor/autoload.php");

$game = new Game(
    new Player(),
    new Field(10,10, 1000)
);

$game->setDifficult(Difficult::easy());

$game->buildField();