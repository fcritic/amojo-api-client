<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

/**
 * @template T of MiddlewareInterface
 * @return MiddlewareInterface
 */
final class StackMiddleware
{
    private const STACK = [
        DateMiddleware::class,
        ContentMD5Middleware::class,
        SignatureMiddleware::class,
    ];

    /**
     * Можно передать кастомную Middleware которая реализует интерфейс src/Middleware/MiddlewareInterface
     *
     * @param array<class-string<MiddlewareInterface>> $additionalMiddlewareClasses
     * @return MiddlewareInterface[]
     */
    public static function create(array $additionalMiddlewareClasses = []): array
    {
        $stack = [];

        foreach (array_merge($additionalMiddlewareClasses, self::STACK) as $class) {
            $stack[] = new $class();
        }

        return $stack;
    }
}
