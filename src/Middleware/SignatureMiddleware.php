<?php

declare(strict_types=1);

namespace AmoJo\Middleware;

use AmoJo\Enum\HeaderType;
use Closure;
use Psr\Http\Message\RequestInterface;

/**
 * @see HeaderType::SIGNATURE
 *
 * @description Подпись запроса. Формируется строка из названия метода (GET/POST) в верхнем регистре
 * и значений (как указаны в запросе без изменений) заголовков путем объединения через "\n".
 * Значения заголовков идут в определенном порядке. В общем случае если заголовок отсутствует,
 * вместо него указывается пустая строка.
 * Далее к строке добавляем запрашиваемый путь из url без протокола и домена (без GET параметров).
 * Получившуюся строку обсчитываем по HMAC-SHA1, а в качестве секрета используем секрет канала,
 * полученный при регистрации. Получившийся хеш в нижнем регистре указываем в заголовке X-Signature
 *
 * @implements MiddlewareInterface
 */
final class SignatureMiddleware implements MiddlewareInterface
{
    /** @var string */
    private string $secretKey;

    /**
     * @param string $secretKey
     */
    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        /**
         * @return RequestInterface
         */
        return function (RequestInterface $request, array $options) use ($handler) {

            $str = implode("\n", [
                strtoupper($request->getMethod()),
                $request->getHeaderLine(HeaderType::CONTENT_MD5),
                $request->getHeaderLine(HeaderType::CONTENT_TYPE),
                $request->getHeaderLine(HeaderType::DATE),
                $request->getUri()->getPath()
            ]);

            $signature = strtolower(hash_hmac('sha1', $str, $this->secretKey));

            /** @var RequestInterface $request */
            $request = $request->withHeader(HeaderType::SIGNATURE, $signature);

            return $handler($request, $options);
        };
    }
}
