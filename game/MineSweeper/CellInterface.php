<?php

namespace Game\MineSweeper;

interface CellInterface
{
    public function getX() : int;
    public function getY() : int;
}