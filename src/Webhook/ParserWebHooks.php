<?php

declare(strict_types=1);

namespace AmoJo\Webhook;

use AmoJo\Exception\InvalidRequestWebHookException;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Messages\MessageFactory;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Models\Users\ValueObject\UserProfile;

class ParserWebHooks
{
    /**
     * @throws InvalidRequestWebHookException
     */
    public function parse(string $requestBody): Payload
    {
        $data = $this->parseJson($requestBody);

        return $this->createPayload($data);
    }

    private function parseJson(string $json): array
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidRequestWebHookException('Invalid JSON format');
        }
    }

    private function createPayload(array $data): Payload
    {
        $messageData = $data['message'];

        return (new Payload())
            ->setConversation($this->parseConversation($messageData['conversation']))
            ->setSender($this->parseSender($messageData['sender']))
            ->setReceiver($this->parseReceiver($messageData['receiver']))
            ->setMessage($this->parseMessage($messageData['message']))
            ->setReplyTo($this->parseReplyTo($messageData['message']));
    }

    private function parseConversation(array $data): Conversation
    {
        return (new Conversation())
            ->setRefId($data['id'])
            ->setId($data['client_id'] ?? null);
    }

    private function parseSender(array $data): SenderInterface
    {
        return (new Sender())
            ->setRefId($data['id'])
            ->setName($data['name']);
    }

    private function parseReceiver(array $data): ReceiverInterface
    {
        return (new Receiver())
            ->setRefId($data['id'])
            ->setId($data['client_id'] ?? null)
            ->setName($data['name'])
            ->setProfile(
                (new UserProfile())
                    ->setPhone($data['phone'] ?? '')
                    ->setEmail($data['email'] ?? '')
            );
    }

    private function parseMessage(array $data): MessageInterface
    {
        $message = MessageFactory::create($data['type']);

        $message
            ->setRefUid($data['id'])
            ->setText($data['text'] ?? '')
            ->setTimestamp($data['timestamp'])
            ->setMsecTimestamp($data['msec_timestamp']);

        $this->parseOptionalFields($message, $data);

        return $message;
    }

    private function parseOptionalFields(MessageInterface $message, array $data): void
    {
        if (isset($data['media'])) {
            $message->setMedia($data['media']);
        }

        if (isset($data['file_name'])) {
            $message->setFileName($data['file_name']);
        }

        if (isset($data['file_size'])) {
            $message->setFileSize((int)$data['file_size']);
        }

        if (isset($data['reply_to'])) {
            $message->setReplyTo($this->parseReply($data['reply_to']));
        }
    }

    private function parseReply(array $data): ?MessageReply
    {
        if (!isset($data['message'])) {
            return null;
        }

        return new MessageReply(
            $this->parseMessage($data['message'])
        );
    }
}
