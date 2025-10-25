<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Enum\AmoJoHookAction;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Messages\ReplyTo;

final class AmoJoHookOutgoingMessage extends AbstractAmoJoHook
{
    private MessageInterface $message;
    private UserInterface $sender;
    private UserInterface $receiver;
    private ?ReplyTo $replyTo = null;
    private ?string $source = null;
    private int $timestamp;
    private int $msecTimestamp;

    public function __construct(
        string $accountUuid,
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
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->source = $source;
        $this->timestamp = $timestamp;
        $this->msecTimestamp = $msecTimestamp;
        $this->message = $message;
        $this->replyTo = $replyTo;

        parent::__construct($accountUuid, $time, $sender, $conversation);
    }

    public function getType(): string
    {
        return AmoJoHookAction::MESSAGE;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getSender(): SenderInterface
    {
        /** @var SenderInterface */
        return $this->sender;
    }

    public function getReceiver(): ReceiverInterface
    {
        /** @var ReceiverInterface */
        return $this->receiver;
    }

    public function getReplyTo(): ?ReplyTo
    {
        return $this->replyTo;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getMsecTimestamp(): int
    {
        return $this->msecTimestamp;
    }

    public function toArray(): array
    {
        $message = array_merge(['id' => $this->getMessage()->getRefUuid()], $this->getMessage()->toPayload());

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
