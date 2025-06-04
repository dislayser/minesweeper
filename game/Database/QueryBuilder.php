<?php

declare(strict_types=1);

namespace Game\Database;

use Game\Service\Util\StringUtil;
use Game\Exception\DatabaseException;

class QueryBuilder
{
    public const TAB = "    ";
    public const SORT_ASC = "ASC";
    public const SORT_DESC = "DESC";
    public const JOIN_INNER = "INNER JOIN";
    public const JOIN_LEFT = "LEFT JOIN";

    private array $select = [];
    private array $from = [
        "table" => null,
        "as" => null
    ];
    private array $joins = [];
    private array $andWhere = [];
    private array $orWhere = [];
    private array $params = [];
    private array $order = [];
    private ?int $limit = null;
    private ?int $offset = null;


    public function __construct() {}

    public function select(array|string $select = []) : static
    {
        if (is_string($select)) {
            $select = explode(", ", $select);
        }
        $select = array_values($select);
        $this->select = [...$this->select, ...$select];
        return $this;
    }

    public function from(string $table, ?string $as = null) : static
    {
        $this->from["table"] = $table;
        $this->from["as"] = $as;
        return $this;
    }

    // Inner Join
    public function join(string $table, string $as, string $on, ?string $type = null) : static
    {
        $this->joins[] = [
            "type" => $type ?? self::JOIN_INNER,
            "table" => $table,
            "as" => $as,
            "on" => $on
        ];
        return $this;
    }

    public function leftJoin(string $table, string $as, string $on) : static
    {
        $this->join($table, $as, $on, self::JOIN_LEFT);
        return $this;
    }

    public function innerJoin(string $table, string $as, string $on) : static
    {
        $this->join($table, $as, $on, self::JOIN_INNER);
        return $this;
    }

    public function andWhere(string $where) : static
    {
        $this->andWhere[] = $where;
        return $this;
    }

    public function orWhere(string $where) : static
    {
        $this->orWhere[] = $where;
        return $this;
    }

    public function addParam(int|string $key, mixed $param) : static
    {
        $this->params[$key] = $param;
        return $this;
    }

    public function addParams(array $params = []) : static
    {
        foreach ($params as $key => $param) {
            $this->addParam($key, $param);
        }
        return $this;
    }

    public function order(string $by, string $sort = self::SORT_ASC) : static
    {
        $sort = StringUtil::upper($sort);
        if (!in_array($sort, [self::SORT_ASC, self::SORT_DESC])) {
            $sort = self::SORT_ASC;
        }
        $this->order[] = [
            "by" => $by,
            "sort" => $sort
        ];
        return $this;
    }
    
    public function limit(int $limit, int $offset = 0) : static
    {
        $this->limit = max($limit, 1);
        $this->offset =  max($offset, 0);
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function buildCount() : string
    {
        if (empty($this->from["table"])) throw new DatabaseException("Не указана таблица.");
        if ($this->from["as"]){
            $from = $this->from["table"] . " " . $this->from["as"];
        }else{
            $from = $this->from["table"];
        }
        $sql = <<<SQL
        SELECT
        COUNT(*) as count
        FROM $from
        $join
        SQL;
        return $sql;
    }
    
    public function build() : string
    {
        if (empty($this->from["table"])) throw new DatabaseException("Не указана таблица.");
        if ($this->from["as"]){
            $from = $this->from["table"] . " " . $this->from["as"];
        }else{
            $from = $this->from["table"];
        }
        $this->select = array_filter($this->select, function($select) : bool {
            $select = trim($select);
            return !empty($select);
        });
        if (!empty($this->select)){
            $select = "    " . implode("," . PHP_EOL . "    ", $this->select);
        }else{
            $select = "    " . $this->from["as"] . ".*" ?? $this->from["table"] . ".*";
        }
        $join = [];
        foreach ($this->joins as $item) {
            $join[] = "{$item["type"]} {$item["table"]} {$item["as"]} ON {$item["on"]}";
        }
        $join = implode(PHP_EOL,  $join);
        $orWhere = implode(" OR " . PHP_EOL . "    ", $this->orWhere);
        if (!empty($orWhere)){
            $orWhere = <<<SQL
            (
                $orWhere
                )
            SQL;
        } 
        $andWhere = [$orWhere, ...$this->andWhere];
        $andWhere = array_filter($andWhere);

        $where = "    " . implode(" AND " . PHP_EOL . "    ", $andWhere);
        if (!empty(trim($where))){
            $where = "WHERE " . PHP_EOL . $where;
        }
        $order = "";
        $sorts = [];
        foreach ($this->order as $value) {
            if (isset($value["by"], $value["sort"])){
                $sorts[] = "{$value["by"]} {$value["sort"]}";
            }
        }
        if (!empty($sorts)){
            $order = "ORDER BY " . implode(", ", $sorts);
        }
        $limit = "";
        if ($this->limit > 0){
            $limit .= "LIMIT {$this->limit}";
            if ($this->offset !== null){
                $limit .= " OFFSET {$this->offset}";
            }
        }

        $sql = <<<SQL
        SELECT
        $select
        FROM $from
        $join
        $where
        $order
        $limit
        SQL;
        return $sql;
    }
    
    public function __toString() : string
    {
        return $this->build();
    }
}