<?php

namespace Game\Service\Util;

class JsonUtil
{
    public static function parse(mixed $data) : string|bool
    {
        return json_encode($data);
    }
    public static function stringify(string $data) : mixed
    {
        return json_decode($data, true);
    }
}