<?php
define( "DEBUG",1);
ini_set('display_errors', DEBUG);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../cache/logs/php_errors.log');