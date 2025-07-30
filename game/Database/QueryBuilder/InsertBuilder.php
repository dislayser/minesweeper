<?php

declare(strict_types=1);

namespace Game\Database\QueryBuilder;

use Game\Service\Util\StringUtil;

class InsertBuilder
{
    protected string $table;
    private ?array $columns;
    private ?array $values;
    private array $params;

    public function __construct() {
    }

    public function insert(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function columns(array $columns): static
    {
        foreach ($columns as $column) {
            if (is_string($column) && !empty($column) && !in_array($column, $this->columns)) {
                $this->columns[] = StringUtil::toSnakeCase($column);
            }
        }
        return $this;
    }

    public function values(array $values): static
    {
        foreach ($values as $value) {
            if (
                !empty($value) &&
                is_string($value) &&
                !in_array($value, $this->values) &&
                str_starts_with($value, ":") // TODO: Возможно стоит убрать проверку на двоеточие
            ) {
                $this->values[] = StringUtil::clearSpaces($value);
            }
        }
        return $this;
    }

    public function addParam(int|string $key, mixed $param) : static
    {
        $this->params[StringUtil::clearSpaces($key)] = $param;
        return $this;
    }

    public function addParams(array $params = []) : static
    {
        foreach ($params as $key => $param) {
            $this->addParam($key, $param);
        }
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}