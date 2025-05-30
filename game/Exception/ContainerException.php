<?php

declare(strict_types=1);

namespace Game\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends GameException implements ContainerExceptionInterface
{
}