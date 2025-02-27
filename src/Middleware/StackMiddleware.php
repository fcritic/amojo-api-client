<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

/**
 * @template T of MiddlewareInterface
 * @return MiddlewareInterface
 */
final class StackMiddleware
{
    /**
     * Можно передать кастомную Middleware которая реализует интерфейс src/Middleware/MiddlewareInterface
     *
     * @param array<class-string<MiddlewareInterface>> $additionalMiddlewareClasses
     * @return MiddlewareInterface[]
     */
    public static function create(array $additionalMiddlewareClasses = []): array
    {
        $core = [];

        /** @var array<class-string<MiddlewareInterface>> $stack */
        $stack = [
            DateMiddleware::class,
            ContentMD5Middleware::class,
            SignatureMiddleware::class,
        ];

        foreach (array_merge($additionalMiddlewareClasses, $stack) as $class) {
            $core[] = new $class();
        }

        return $core;
    }
}
