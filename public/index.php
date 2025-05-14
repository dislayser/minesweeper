<?php

use Game\Kernel;

require_once(dirname(__FILE__) . "/../vendor/autoload.php");

$app = new Kernel();
$app->run();