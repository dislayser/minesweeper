<?php

declare(strict_types=1);

namespace Game\Kernel;

use Game\Kernel\InputBag\CookieBag;
use Game\Kernel\InputBag\ServerBag;
use Game\Kernel\InputBag\SessionBag;

class InputBag
{
    protected mixed $data;
    public function __construct(mixed $data = []) {
        $this->data = $data;
    }

    public function has($keys): bool
    {
        $keys = $this->parseKeys($keys);
        $result = $this->data;
        foreach ($keys as $key) {
            if (isset($result[$key])){
                $result = $result[$key];
            } else {
                return false;
            }
        }
        return true;
    }

    public function get(array|string $keys, mixed $default = null) : mixed
    {
        $keys = $this->parseKeys($keys);
        $result = $this->data;
        foreach ($keys as $key) {
            if (isset($result[$key])){
                $result = $result[$key];
            } else {
                $result = null;
            }
        }
        return $result;
    }

    public function getAll() : mixed
    {
        return $this->data;
    }

    public function getInt(array|string $keys, ?int $default = null) : ?int
    {
        $result = $this->get($keys, $default);
        if ($result === $default) {
            return $default;
        }else{
            return (int) $result;
        }
    }

    public function getStr(array|string $keys, ?string $default = null) : ?string
    {
        $result = $this->get($keys, $default);
        if ($result === $default) {
            return $default;
        }else{
            return (string) $result;
        }
    }

    public function query() : self
    {
        return new self($_GET);
    }

    public function files() : self
    {
        return new self($_FILES);
    }

    public function post() : self
    {
        return new self($_POST);
    }

    public function session() : self
    {
        return new SessionBag($_SESSION);
    }

    public function env() : self
    {
        return new self($_ENV);
    }

    public function server() : self
    {
        return new ServerBag($_SERVER);
    }

    public function cookie() : self
    {
        return new CookieBag($_COOKIE);
    }

    protected function parseKeys(array|string $keys) : array
    {
        if (is_string($keys)) $keys = explode(".", $keys);
        $keys = array_values($keys);
        return $keys;
    }
}