<?php

namespace Game\Kernel;

class InputBag
{
    protected array $data;
    public function __construct(array $data = []) {
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

    public function post() : self
    {
        return new self($_POST);
    }

    public function session() : self
    {
        return new self($_SESSION);
    }

    public function server() : self
    {
        return new self($_SERVER);
    }

    public function cookie() : self
    {
        return new self($_COOKIE);
    }

    protected function parseKeys(array|string $keys) : array
    {
        if (is_string($keys)) $keys = explode(".", $keys);
        $keys = array_values($keys);
        return $keys;
    }
}