<?php

declare(strict_types=1);

namespace Game\Controller;

use Game\Kernel\InputBag;
use Game\Service\AIApi;

class TestController extends Controller
{
    public function index(): void
    {
        $key = (new InputBag())->env()->getStr("DEEP_SEEK");
        $api = new AIApi(
            $key,
            "https://api.deepseek.com"
        );
        $api->get();
        echo "<br>";
    }
}