<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Messages\MessageFactory;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Webhook\Traits\UserBuilderTrait;

final class HistoryChatResponse implements ResponseInterface
{
    use UserBuilderTrait;

    private array $messages = [];

    public function __construct(array $messagesData)
    {
        $this->parseMessages($messagesData);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    private function parseMessages(array $messagesData): void
    {
        foreach ($messagesData as $messageData) {
            $this->messages[] = (new Payload())
                ->setMessage($this->createMessage($messageData))
                ->setSender($this->buildUser($messageData['sender'], Sender::class))
                ->setReceiver($this->buildUser($messageData['receiver'] ?? [], Receiver::class));
        }
    }

    private function createMessage(array $messageData): MessageInterface
    {
        return (new MessageFactory())->create($messageData);
    }

    public static function fromArray(array $data): ResponseInterface
    {
        return new self($data['messages'] ?? []);
    }

    public function toArray(): array
    {
        $result = [];

        /** @var Payload $payload */
        foreach ($this->messages as $payload) {
            $result[] = [
                'message'  => $payload->getMessage()->toPayload(),
                'sender'   => $payload->getSender() ? $payload->getSender()->toPayload() : [],
                'receiver' => $payload->getReceiver() ? $payload->getReceiver()->toPayload() : [],
            ];
        }

        return ['messages' => $result];
    }
}
