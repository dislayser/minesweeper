<?php

declare(strict_types=1);

namespace Game\Controller;

class GameApiController extends Controller
{
    public function index() : void
    {
        $this->json(["test" => 123]);
    }
}