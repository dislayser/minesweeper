<?php

declare(strict_types=1);

namespace Game\Database\QueryBuilder;

use Game\Service\Util\StringUtil;

class UpdateBuilder
{
    protected string $table;
    private ?array $columns;
    private ?array $values;
    private array $params;

    public function __construct() {
    }

    public function addParam(int|string $key, mixed $param) : static
    {
        $this->params[StringUtil::clearSpaces((string)$key)] = $param;
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