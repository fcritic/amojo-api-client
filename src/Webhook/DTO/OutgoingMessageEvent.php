<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Enum\WebHookType;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Messages\ReplyTo;

/**
 * События исходящего сообщения
 *
 * @extends AbstractWebHookEvent
 */
final class OutgoingMessageEvent extends AbstractWebHookEvent
{
    /** @var MessageInterface */
    private MessageInterface $message;

    /** @var SenderInterface */
    private UserInterface $sender;

    /** @var ReceiverInterface */
    private UserInterface $receiver;

    /** @var ReplyTo|null */
    private ?ReplyTo $replyTo = null;

    /** @var string|null */
    private ?string $source = null;

    /** @var int */
    private int $timestamp;

    /** @var int */
    private int $msecTimestamp;

    /**
     * @param string $accountUid
     * @param int $time
     * @param UserInterface $receiver
     * @param UserInterface $sender
     * @param string|null $source
     * @param Conversation $conversation
     * @param int $timestamp
     * @param int $msecTimestamp
     * @param MessageInterface $message
     * @param ReplyTo|null $replyTo
     */
    public function __construct(
        string $accountUid,
        int $time,
        UserInterface $receiver,
        UserInterface $sender,
        ?string $source,
        Conversation $conversation,
        int $timestamp,
        int $msecTimestamp,
        MessageInterface $message,
        ?ReplyTo $replyTo
    ) {
        parent::__construct($accountUid, $time, $sender, $conversation);

        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->source = $source;
        $this->timestamp = $timestamp;
        $this->msecTimestamp = $msecTimestamp;
        $this->message = $message;
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return WebHookType::MESSAGE;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * @return SenderInterface
     */
    public function getSender(): SenderInterface
    {
        return $this->sender;
    }

    /**
     * @return ReceiverInterface
     */
    public function getReceiver(): ReceiverInterface
    {
        return $this->receiver;
    }

    /**
     * @return ReplyTo|null
     */
    public function getReplyTo(): ?ReplyTo
    {
        return $this->replyTo;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getMsecTimestamp(): int
    {
        return $this->msecTimestamp;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $message = array_merge(['id' => $this->getMessage()->getRefUid()], $this->getMessage()->toPayload());

        if ($this->getReplyTo() !== null) {
            $replyTo['reply_to'] = $this->getReplyTo()->toPayload();
            $message = array_merge($message, $replyTo);
        }

        return [
            ...parent::toArray(),
            $this->getType() => [
                'receiver' => $this->getReceiver()->toPayload(),
                'sender' => $this->getSender()->toPayload(),
                'source' => [
                    'external_id' => $this->getSource(),
                ],
                'conversation' => $this->getConversation()->toArray(),
                'timestamp' => $this->getTimestamp(),
                'msec_timestamp' => $this->getMsecTimestamp(),
                'message' => $message
            ]
        ];
    }
}
