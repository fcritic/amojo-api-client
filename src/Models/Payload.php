<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\SenderException;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Messages\ReplyTo;

use function array_filter;

/**
 * Объект запроса для отправки/редактирования сообщения
 *
 * Также используется в качестве моделей сообщения
 */
class Payload
{
    /** @var Conversation */
    private Conversation $conversation;

    /** @var MessageInterface */
    private MessageInterface $message;

    /** @var SenderInterface|null*/
    private ?SenderInterface $sender = null;

    /** @var ReceiverInterface|null */
    private ?ReceiverInterface $receiver = null;

    /**
     * @var ReplyTo|null
     */
    private ?ReplyTo $replyTo = null;

    /** @var bool */
    private bool $silent = false;

    /**
     * Модель чата
     *
     * @param Conversation $conversation
     * @return Payload
     */
    public function setConversation(Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * Модель отправителя
     *
     * @param SenderInterface $sender
     * @return Payload
     */
    public function setSender(UserInterface $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Модель получателя
     *
     * @param ReceiverInterface|null $receiver
     * @return Payload
     */
    public function setReceiver(?UserInterface $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * Модель сообщения
     *
     * @param MessageInterface $message
     * @return Payload
     */
    public function setMessage(MessageInterface $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Нужно ли создавать неразобранное и отправлять уведомление по сообщению в аккаунте amoCRM.
     * При редактировании сообщения неразобранное не создаётся и уведомление не отправляется
     *
     * @param bool $silent
     * @return Payload
     */
    public function setSilent(bool $silent): self
    {
        $this->silent = $silent;
        return $this;
    }

    /**
     * Модель отправителя
     *
     * @return SenderInterface
     */
    public function getSender(): SenderInterface
    {
        return $this->sender;
    }

    /**
     * Модель сообщения
     *
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * Модель получателя
     *
     * @return ReceiverInterface|null
     */
    public function getReceiver(): ?ReceiverInterface
    {
        return $this->receiver;
    }

    public function setReplyTo(ReplyTo $replyTo): self
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * Валидация.
     *
     * @param bool $isEdit
     * @return array
     */
    protected function validatePayload(bool $isEdit): array
    {
        $sender = $this->sender;
        $receiver = $this->receiver;
        $replyTo = $this->replyTo;

        if (!$isEdit) {
            if (! isset($this->conversation, $this->message, $this->sender)) {
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
        } elseif (! isset($this->conversation, $this->message)) {
            throw new AmoJoException('To edit a message, the required parameter Conversation or Message, is not set');
        }

        return [
            'sender'   => $sender,
            'receiver' => $receiver,
            'replyTo'  => $replyTo
        ];
    }

    /**
     * Метод для получения модели сообщения при получении истории чата
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'timestamp'      => $this->message->getTimestamp(),
            'msec_timestamp' => $this->message->getMsecTimestamp(),
            'msgid'          => $this->message->getUid(),
            'sender'         => $this->sender->toPayload(),
            'receiver'       => $this->receiver->toPayload(),
            'message'        => $this->message->toPayload()
        ];
    }

    /**
     * Метод отправки сообщения по API
     *
     * @param bool $isEdit
     * @return array
     */
    public function toApi(bool $isEdit = false): array
    {
        $data = $this->validatePayload($isEdit);

        $payload = [
            'timestamp'           => $this->message->getTimestamp(),
            'msec_timestamp'      => $this->message->getMsecTimestamp(),
            'conversation_id'     => $this->conversation->getId(),
            'conversation_ref_id' => $this->conversation->getRefId(),
            'msgid'               => $this->message->getUid(),
            'sender'              => $data['sender'],
            'receiver'            => $data['receiver'],
            'message'             => $this->message->toPayload(),
            'reply_to'            => $data['replyTo'],
            'silent'              => $this->silent,
        ];

        return array_filter($payload, static function ($value) {
            return $value !== null;
        });
    }
}
