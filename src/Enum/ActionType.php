<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Доступные методы в сервисе API чатов
 */
class ActionType
{
    public const CONNECT = 'connect';
    public const DISCONNECT = 'disconnect';
    public const CHAT = 'chats';
    public const MESSAGE = 'message';
    public const DELIVERY_STATUS = 'delivery_status';
    public const GET_HISTORY = 'history';
    public const TYPING = 'typing';
    public const REACT = 'react';
}
