<?php

declare(strict_types=1);

namespace AmoJo\DTO;

use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Messages\ContactMessage;
use AmoJo\Models\Messages\LocationMessage;
use AmoJo\Models\Messages\MessageFactory;
use AmoJo\Models\Messages\StickerMessage;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Webhook\Traits\UserParserTrait;

/**
 * DTO ответа при получении истории чата
 *
 * @extends AbstractResponse
 */
final class HistoryChatResponse extends AbstractResponse
{
    use UserParserTrait;

    /** @var Payload[] массив с сообщениями */
    private array $messages = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->parseMessages($data['messages'] ?? []);
    }

    /**
     * Получения массива с сообщениями
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Парсит массив на объекты
     *
     * @param array $messagesData
     * @return void
     */
    private function parseMessages(array $messagesData): void
    {
        foreach ($messagesData as $messageData) {
            $this->messages[] = (new Payload())
                ->setMessage(
                    $this->createMessage($messageData)
                )
                ->setSender(
                    $this->parseUser($messageData['sender'], Sender::class)
                )
                ->setReceiver(
                    $this->parseUser($messageData['receiver'] ?? [], Receiver::class)
                );
        }
    }

    /**
     * Создания объекта сообщения для модели сообщения
     *
     * @param array $messageData
     * @return MessageInterface
     */
    private function createMessage(array $messageData): MessageInterface
    {
        return (new MessageFactory())->create($messageData);
    }
}
