<?php

declare(strict_types=1);

namespace AmoJo\DTO;

use AmoJo\Enum\ActionsType;
use InvalidArgumentException;

/**
 * Фабрика ответа. Возвращает DTO
 */
class ResponseFactory
{
    /** @var string[] */
    private const FACTORIES = [
        ActionsType::MESSAGE         => MessageResponse::class,
        ActionsType::CONNECT         => ConnectResponse::class,
        ActionsType::DISCONNECT      => DisconnectResponse::class,
        ActionsType::CHAT            => CreateChatResponse::class,
        ActionsType::DELIVERY_STATUS => DeliveryResponse::class,
        ActionsType::GET_HISTORY     => HistoryChatResponse::class,
        ActionsType::TYPING          => TypingResponse::class,
        ActionsType::REACT           => ReactResponse::class,
    ];

    /**
     * @param string $action Тип действия из ActionsType
     * @param array $data Данные ответа
     * @return AbstractResponse
     * @throws InvalidArgumentException Если действие не поддерживается
     */
    public static function create(string $action, array $data): AbstractResponse
    {
        if (!isset(self::FACTORIES[$action])) {
            throw new InvalidArgumentException("Unsupported action: {$action}");
        }

        $factoryClass = self::FACTORIES[$action];
        return new $factoryClass($data);
    }
}
