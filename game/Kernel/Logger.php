<?php

declare(strict_types=1);

namespace Game\Kernel;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private array $logs = [];

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->logs[] = [
            "datetime" => new \DateTime(),
            "level" => $level,
            "message" => $message,
            "context" => $context
        ];
    }
}