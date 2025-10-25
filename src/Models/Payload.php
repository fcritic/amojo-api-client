<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\SenderException;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Messages\ReplyTo;

use function array_filter;

class Payload
{
    private Conversation $conversation;
    private MessageInterface $message;
    private ?SenderInterface $sender = null;
    private ?ReceiverInterface $receiver = null;
    private ?ReplyTo $replyTo = null;
    private bool $silent = false;

    public function setConversation(Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function setSender(SenderInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function setReceiver(?ReceiverInterface $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function setMessage(MessageInterface $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Нужно ли создавать неразобранное и отправлять уведомление по сообщению в аккаунте amoCRM.
     * При редактировании сообщения неразобранное не создаётся и уведомление не отправляется
     */
    public function setSilent(bool $silent): self
    {
        $this->silent = $silent;

        return $this;
    }

    public function getSender(): SenderInterface
    {
        return $this->sender;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getReceiver(): ?ReceiverInterface
    {
        return $this->receiver;
    }

    public function setReplyTo(ReplyTo $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    protected function validatePayload(bool $isEdit): array
    {
        $sender = $this->sender;
        $receiver = $this->receiver;
        $replyTo = $this->replyTo;

        if (!$isEdit) {
            if (!isset($this->conversation, $this->message, $this->sender)) {
                throw new AmoJoException('The required parameter Conversation, or Message, or Sender is not set');
            }

            if ($replyTo instanceof ReplyTo) {
                $replyTo = $this->replyTo->toPayload();
            }

            if ($sender instanceof SenderInterface) {
                $sender = $sender->toPayload();
            }

            if ($this->receiver instanceof ReceiverInterface) {
                $receiver = $this->receiver->toPayload();

                if ($this->sender->getRefId() === null) {
                    throw new SenderException('The user\'s Ref ID parameter is required to send an outgoing message.');
                }
            }
        } elseif (!isset($this->conversation, $this->message)) {
            throw new AmoJoException('To edit a message, the required parameter Conversation or Message, is not set');
        }

        return [
            'sender' => $sender,
            'receiver' => $receiver,
            'replyTo' => $replyTo
        ];
    }

    /**
     * Метод для получения модели сообщения при получении истории чата
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->message->getTimestamp(),
            'msec_timestamp' => $this->message->getMsecTimestamp(),
            'msgid' => $this->message->getUuid(),
            'sender' => $this->sender->toPayload(),
            'receiver' => $this->receiver->toPayload(),
            'message' => $this->message->toPayload()
        ];
    }

    /**
     * Метод отправки сообщения по API
     */
    public function toApi(bool $isEdit = false): array
    {
        $data = $this->validatePayload($isEdit);

        $payload = [
            'timestamp' => $this->message->getTimestamp(),
            'msec_timestamp' => $this->message->getMsecTimestamp(),
            'conversation_id' => $this->conversation->getId(),
            'conversation_ref_id' => $this->conversation->getRefId(),
            'msgid' => $this->message->getUuid(),
            'sender' => $data['sender'],
            'receiver' => $data['receiver'],
            'message' => $this->message->toPayload(),
            'reply_to' => $data['replyTo'],
            'silent' => $this->silent,
        ];

        return array_filter($payload, static function ($value) {
            return $value !== null;
        });
    }
}
