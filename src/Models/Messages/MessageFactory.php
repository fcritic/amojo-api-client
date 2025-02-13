<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Models\Interfaces\MessageInterface;
use InvalidArgumentException;

/**
 * Фабрика сообщений
 */
class MessageFactory
{
    /** @var string[] */
    private const FACTORIES = [
        MessageType::TEXT     => TextMessage::class,
        MessageType::CONTACT  => ContactMessage::class,
        MessageType::FILE     => FileMessage::class,
        MessageType::VOICE    => VoiceMessage::class,
        MessageType::AUDIO    => AudioMessage::class,
        MessageType::LOCATION => LocationMessage::class,
        MessageType::PICTURE  => PictureMessage::class,
        MessageType::VIDEO    => VideoMessage::class,
        MessageType::STICKER  => StickerMessage::class
    ];

    /**
     * @param string $messageType
     * @return MessageInterface
     */
    public static function create(string $messageType): MessageInterface
    {
        if (!isset(self::FACTORIES[$messageType])) {
            throw new InvalidArgumentException("Unsupported message type: {$messageType}");
        }

        $factoryClass = self::FACTORIES[$messageType];
        return new $factoryClass();
    }
}
