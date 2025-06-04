<?php

namespace Game\Controller;

class IndexController extends Controller
{
    public function index() : void
    {
        $this->render("game/index.twig", []);
    }
}