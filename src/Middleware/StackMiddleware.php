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
     * @param string $secretKey
     * @param array<class-string<MiddlewareInterface>> $additionalMiddlewareClasses
     * @return MiddlewareInterface[]
     */
    public static function create(string $secretKey, array $additionalMiddlewareClasses = []): array
    {
        $core = [
            new DateMiddleware(),
            new ContentMD5Middleware(),
            new SignatureMiddleware($secretKey),
        ];

        foreach ($additionalMiddlewareClasses as $class) {
            $core[] = new $class();
        }

        return $core;
    }
}
