<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Доступные события для сообщения
 */
class EventType
{
    /** @var string */
    public const NEW_MESSAGE = 'new_message';

    /** @var string */
    public const EDIT_MESSAGE = 'edit_message';
}
