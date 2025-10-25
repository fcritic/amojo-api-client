<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

use Closure;

interface MiddlewareInterface
{
    public function __invoke(callable $handler): Closure;
}
