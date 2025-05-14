<?php

namespace Game\Kernel;

class Session
{
    public function __construct() {}

    public function start() : void
    {
        if (session_status() === PHP_SESSION_NONE || empty($_SESSION)) session_start();
    }

    public function id() : string
    {
        return session_id();
    }

    public function storage() : InputBag
    {
        return new InputBag($_SESSION);
    }
}