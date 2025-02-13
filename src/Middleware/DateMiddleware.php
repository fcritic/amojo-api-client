<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

use AmoJo\Enum\HeaderType;
use Closure;
use DateTimeInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @see HeaderType::DATE
 *
 * @description Дата и время формирования запроса, подпись будет действительна 15 минут от даты формирования запроса
 *
 * @implements MiddlewareInterface
 */
final class DateMiddleware implements MiddlewareInterface
{
    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        /**
         * @return RequestInterface
         */
        return static function (RequestInterface $request, array $options) use ($handler) {

            /** @var RequestInterface $request */
            $request = $request->withHeader(HeaderType::DATE, date(DateTimeInterface::RFC2822));

            return $handler($request, $options);
        };
    }
}
