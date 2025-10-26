<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\UnsupportedMessageTypeException;
use AmoJo\Models\Interfaces\MessageInterface;

class MessageFactory
{
    public const TYPE_MAPPING = [
        MessageType::TEXT => TextMessage::class,
        MessageType::CONTACT => ContactMessage::class,
        MessageType::FILE => FileMessage::class,
        MessageType::VOICE => VoiceMessage::class,
        MessageType::AUDIO => AudioMessage::class,
        MessageType::LOCATION => LocationMessage::class,
        MessageType::PICTURE => PictureMessage::class,
        MessageType::VIDEO => VideoMessage::class,
        MessageType::STICKER => StickerMessage::class
    ];

    public function create(array $data): MessageInterface
    {
        $messageType = $data['message']['type'] ?? 'text';

        if (!isset(self::TYPE_MAPPING[$messageType])) {
            throw new UnsupportedMessageTypeException(sprintf('Unsupported message type: %s', $messageType));
        }

        $class = self::TYPE_MAPPING[$messageType];
        $message = new $class();

        $this->initializeBaseProperties($message, $data);
        $this->populateTypeProperties($message, $data['message']);

        return $message;
    }

    private function initializeBaseProperties(MessageInterface $message, array $data): void
    {
        $message
            ->setRefUuid($data['message']['id'] ?? '')
            ->setUuid($data['message']['client_id'] ?? '')
            ->setText($data['message']['text'] ?? '')
            ->setTimestamp($data['timestamp'] ?? $data['message']['timestamp'])
            ->setMsecTimestamp($data['msec_timestamp'] ?? $data['message']['msec_timestamp']);
    }

    private function populateTypeProperties(MessageInterface $message, array $data): void
    {
        switch (true) {
            case $message instanceof FileMessage:
            case $message instanceof PictureMessage:
            case $message instanceof VideoMessage:
                $message
                    ->setMedia($data['media'])
                    ->setFileName($data['file_name'])
                    ->setFileSize($data['file_size']);
                break;

            case $message instanceof LocationMessage:
                $message
                    ->setLat($data['location']['lat'])
                    ->setLon($data['location']['lon']);
                break;

            case $message instanceof ContactMessage:
                $message
                    ->setName($data['contact']['name'])
                    ->setPhone($data['contact']['phone']);
                break;

            case $message instanceof AudioMessage:
            case $message instanceof VoiceMessage:
                $message
                    ->setMedia($data['media'])
                    ->setFileName($data['file_name'])
                    ->setFileSize($data['file_size'])
                    ->setMediaDuration($data['media_duration']);
                break;

            case $message instanceof TextMessage:
                break;

            default:
                throw new UnsupportedMessageTypeException(
                    sprintf('Unsupported message type: %s', $message['message']['type'])
                );
        }
    }
}
