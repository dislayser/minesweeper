<?php

declare(strict_types=1);

namespace Game\Service\Util;

class StringUtil
{
    /**
     * Преобразует строку в camelCase.
     * Пример: "hello_world" -> "helloWorld"
     */
    public static function toCamelCase(string $str): string
    {
        $str = self::lower($str);
        $str = self::clearSpaces($str);
        $str = preg_replace_callback('/_([a-z])/', function ($matches) {
            return strtoupper($matches[1]); // Преобразуем символ после "_" в верхний регистр
        }, $str);
        return $str;
    }

    /**
     * Преобразует строку в snake_case.
     * Пример: "helloWorld" -> "hello_world"
     */
    public static function toSnakeCase(string $str, bool $upper = false): string
    {
        $str = self::clearSpaces($str);
        $str = preg_replace_callback('/([a-z])([A-Z])/', function ($matches) {
            return $matches[1] . '_' . strtolower($matches[2]); // Вставляем "_" между нижним и верхним регистром
        }, $str);
        return $upper ? self::upper($str) : self::lower($str);
    }

    /**
     * Удаляет пробелы из строки (в начале, в конце и между словами).
     * Пример: "  hello   world  " -> "helloworld"
     */
    public static function clearSpaces(string $str): string
    {
        return str_replace(' ', '', $str);
    }

    /**
     * Преобразует строку в верхний регистр.
     * Пример: "hello world" -> "HELLO WORLD"
     */
    public static function upper(string $str): string
    {
        return mb_strtoupper($str, "UTF-8");
    }

    /**
     * Преобразует строку в нижний регистр.
     * Пример: "HELLO WORLD" -> "hello world"
     */
    public static function lower(string $str): string
    {
        return mb_strtolower($str, "UTF-8");
    }

    /**
     * Удаляет пробелы в начале и конце строки.
     */
    public static function trim(?string $str): ?string
    {
        if ($str === null) return null;
        return trim($str);
    }
}