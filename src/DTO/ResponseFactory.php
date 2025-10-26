<?php

declare(strict_types=1);

namespace AmoJo\DTO;

use AmoJo\DTO\Response\ConnectResponse;
use AmoJo\DTO\Response\CreateChatResponse;
use AmoJo\DTO\Response\EmptyResponse;
use AmoJo\DTO\Response\HistoryChatResponse;
use AmoJo\DTO\Response\MessageResponse;
use AmoJo\DTO\Response\ResponseInterface;
use AmoJo\Enum\ActionType;

/**
 * Фабрика ответа. Возвращает DTO
 */
class ResponseFactory
{
    /**
     * Все остальные методы имею пустые ответы и на них возвращается
     * @uses EmptyResponse
     */
    private const RESPONSE_MAP = [
        ActionType::MESSAGE => MessageResponse::class,
        ActionType::CONNECT => ConnectResponse::class,
        ActionType::GET_HISTORY => HistoryChatResponse::class,
        ActionType::CHAT => CreateChatResponse::class,
    ];

    public static function create(array $data, string $action = 'empty'): ResponseInterface
    {
        if (isset(self::RESPONSE_MAP[$action])) {
            /** @var ResponseInterface $responseClass */
            $responseClass = self::RESPONSE_MAP[$action];

            return $responseClass::fromArray($data);
        }

        return EmptyResponse::fromArray([]);
    }
}
