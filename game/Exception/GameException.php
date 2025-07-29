<?php

declare(strict_types=1);

namespace Game\Exception;

class GameException extends \Exception
{
    protected $context = [];

    public function __construct(
        string $message = "",
        int $code = 0,
        array $context = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}