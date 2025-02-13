<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

use AmoJo\Enum\HeaderType;
use Closure;
use Psr\Http\Message\RequestInterface;

/**
 * @see HeaderType::CONTENT_MD5
 *
 * @description Для тела запроса необходимо рассчитать md5 хеш и в заголовке указывать в нижнем регистре.
 * При этом важно иметь в виду, что тело запроса обсчитывается как поток байт, без учета окончания json разметки,
 * и если в конце есть "\n" или пробелы они тоже будут учитываться.
 * Для GET запросов, md5 тоже необходимо рассчитать,
 * даже если ничего не передается в теле запроса (получится md5 от пустой строки)
 *
 * @implements MiddlewareInterface
 */
final class ContentMD5Middleware implements MiddlewareInterface
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

            $body = (string) $request->getBody();

            /** @var RequestInterface $request */
            $request = $request->withHeader(HeaderType::CONTENT_MD5, strtolower(md5($body)));

            return $handler($request, $options);
        };
    }
}
