<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Доступные методы в сервисе API чатов
 */
class ActionsType
{
    /** @var string */
    public const CONNECT = '/connect';

    /** @var string */
    public const DISCONNECT = '/disconnect';

    /** @var string */
    public const CHAT = '/chats';

    /** @var string */
    public const MESSAGE = 'message';

    /** @var string */
    public const DELIVERY_STATUS = '/delivery_status';

    /** @var string */
    public const GET_HISTORY = '/history';

    /** @var string */
    public const TYPING = '/typing';

    /** @var string */
    public const REACT = '/react';
}
