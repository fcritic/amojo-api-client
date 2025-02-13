<?php

declare(strict_types=1);

namespace AmoJo\Helpers;

use AmoJo\Enum\HeaderType;
use AmoJo\Exception\InvalidRequestWebHookException;
use Psr\Http\Message\RequestInterface;

/**
 * Валидация веб хуков на исходящие сообщение
 *
 * Валидация по заголовку X-Signature
 */
final class ValidatorWebHooks
{
    /**
     * @param RequestInterface $request Вебхук
     * @param string $secretKey Секретный ключ канала чатов
     * @return bool Ответ при валидации хука
     * @throws InvalidRequestWebHookException
     */
    public static function isValid(RequestInterface $request, string $secretKey): bool
    {
        try {
            $requestBody = trim((string) $request->getBody(), "\n");
            $signature = hash_hmac('sha1', $requestBody, $secretKey);

            $receivedSignature = $request->getHeaderLine(HeaderType::SIGNATURE);

            return $signature === $receivedSignature;
        } catch (\Exception $e) {
            throw new InvalidRequestWebHookException('Invalid webhook signature. Details: ' . $e->getMessage());
        }
    }
}
