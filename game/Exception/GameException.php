<?php

// TODO: допустил ошибку в словах exception ))) - надо будет поправить

declare(strict_types=1);

namespace Game\Exception;

use Exception;
use Throwable;

class GameException extends Exception
{
    protected $context = [];

    public function __construct(
        string $message = "",
        int $code = 0,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}