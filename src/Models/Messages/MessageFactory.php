<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\UnsupportedMessageTypeException;
use AmoJo\Models\Interfaces\MessageInterface;

/**
 * Фабрика сообщений
 */
class MessageFactory
{
    /** @var string[] */
    public const TYPE_MAPPING = [
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
     * Создания сообщения при определении его типа
     *
     * @param array $data array[message]
     * @return MessageInterface
     */
    public function create(array $data): MessageInterface
    {
        $messageType = $data['message']['type'] ?? 'text';

        if (!isset(self::TYPE_MAPPING[$messageType])) {
            throw new UnsupportedMessageTypeException("Unsupported message type: {$messageType}");
        }

        $class = self::TYPE_MAPPING[$messageType];
        $message = new $class();

        $this->hydrateCommonFields($message, $data);
        $this->hydrateSpecificFields($message, $data);

        return $message;
    }

    /**
     * Устанавливает свойства, которые могут быть у любого сообщения
     *
     * @param MessageInterface $message
     * @param array $data
     * @return void
     */
    private function hydrateCommonFields(MessageInterface $message, array $data): void
    {
        $message
            ->setRefUid($data['message']['id'] ?? '')
            ->setUid($data['message']['client_id'] ?? '')
            ->setText($data['message']['text'] ?? '')
            ->setTimestamp($data['timestamp'] ?? $data['message']['timestamp'])
            ->setMsecTimestamp($data['msec_timestamp'] ?? $data['message']['msec_timestamp']);
    }

    /**
     * Устанавливает свойства в зависимости от типа сообщения
     *
     * @param MessageInterface $message
     * @param array $data
     * @return void
     */
    private function hydrateSpecificFields(MessageInterface $message, array $data): void
    {
        switch (true) {
            case $message instanceof FileMessage:
            case $message instanceof PictureMessage:
            case $message instanceof VideoMessage:
                $message
                    ->setMedia($data['message']['media'])
                    ->setFileName($data['message']['file_name'])
                    ->setFileSize($data['message']['file_size']);
                break;

            case $message instanceof LocationMessage:
                $message
                    ->setLat($data['message']['location']['lat'])
                    ->setLon($data['message']['location']['lon']);
                break;

            case $message instanceof ContactMessage:
                $message
                    ->setName($data['message']['contact']['name'])
                    ->setPhone($data['message']['contact']['phone']);
                break;

            case $message instanceof AudioMessage:
            case $message instanceof VoiceMessage:
                $message
                    ->setMedia($data['message']['media'])
                    ->setFileName($data['message']['file_name'])
                    ->setFileSize($data['message']['file_size'])
                    ->setMediaDuration($data['message']['media_duration']);
                break;

            case $message instanceof TextMessage:
                break;

            default:
                throw new UnsupportedMessageTypeException(
                    "Unsupported message type: {$message['message']['type']}"
                );
        }
    }
}
