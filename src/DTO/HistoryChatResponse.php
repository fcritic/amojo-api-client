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

/**
 * DTO ответа при получении истории чата
 *
 * @extends AbstractResponse
 */
final class HistoryChatResponse extends AbstractResponse
{
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
                    $this->createSender($messageData['sender'])
                )
                ->setReceiver(
                    $this->createReceiver($messageData['receiver'] ?? null)
                );
        }
    }

    /**
     * Создания объекта отправителя для модели сообщения
     *
     * @param array $senderData
     * @return SenderInterface
     */
    private function createSender(array $senderData): SenderInterface
    {
        return (new Sender())
            ->setId($senderData['id'] ?? null)
            ->setRefId($senderData['id'] ?? null)
            ->setName($senderData['name'] ?? '');
    }

    /**
     * Создания объекта получателя для модели сообщения, создается при наличии
     *
     * @param array|null $receiverData
     * @return ReceiverInterface|null
     */
    private function createReceiver(?array $receiverData): ?ReceiverInterface
    {
        if (!$receiverData) {
            return null;
        }

        return (new Receiver())
            ->setId($receiverData['id'] ?? null)
            ->setRefId($receiverData['client_id'] ?? null)
            ->setName($receiverData['name'] ?? '');
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
