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

    // Для проверки является ли строка JSON
    public static function isJson(mixed $str): bool
    {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }
}