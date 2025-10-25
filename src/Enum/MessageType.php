<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Доступные типы сообщений
 */
class MessageType
{
    public const AUDIO = 'audio';
    public const CONTACT = 'contact';
    public const FILE = 'file';
    public const LOCATION = 'location';
    public const PICTURE = 'picture';
    public const STICKER = 'sticker';
    public const TEXT = 'text';
    public const VIDEO = 'video';
    public const VOICE = 'voice';
}
