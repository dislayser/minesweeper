<?php

declare(strict_types=1);

namespace Game\Controller;

use Game\Kernel\InputBag;

class TestController extends Controller
{
    public function index(): void
    {
        dd((new InputBag())->server());

        echo "<br>";
    }
}