<?php

namespace Game\Controller;

class IndexController extends Controller
{
    public function index() : void
    {
        return $this->render("base.html.twig", []);
    }
}