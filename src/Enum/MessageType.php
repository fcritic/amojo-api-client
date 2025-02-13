<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Доступные типы сообщений
 */
class MessageType
{
    /** @var string */
    public const AUDIO = 'audio';

    /** @var string */
    public const CONTACT = 'contact';

    /** @var string */
    public const FILE = 'file';

    /** @var string */
    public const LOCATION = 'location';

    /** @var string */
    public const PICTURE = 'picture';

    /** @var string */
    public const STICKER = 'sticker';

    /** @var string */
    public const TEXT = 'text';

    /** @var string */
    public const VIDEO = 'video';

    /** @var string */
    public const VOICE = 'voice';
}
