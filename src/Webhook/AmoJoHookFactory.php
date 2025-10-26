<?php

declare(strict_types=1);

namespace AmoJo\Webhook;

use AmoJo\Enum\AmoJoHookAction;
use AmoJo\Exception\UnsupportedMessageTypeException;
use AmoJo\Models\Messages\MessageFactory;
use AmoJo\Models\Messages\ReplyTo;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Webhook\DTO\AmoJoHookInterface;
use AmoJo\Webhook\DTO\AmoJoHookOutgoingMessage;
use AmoJo\Webhook\DTO\AmoJoHookReaction;
use AmoJo\Webhook\DTO\AmoJoHookTyping;
use AmoJo\Webhook\Traits\ConversationBuilderTrait;
use AmoJo\Webhook\Traits\UserBuilderTrait;

class AmoJoHookFactory
{
    use UserBuilderTrait;
    use ConversationBuilderTrait;

    private MessageFactory $messageFactory;

    public function __construct(MessageFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    public function fromArray(array $data): AmoJoHookInterface
    {
        switch (true) {
            case isset($data[AmoJoHookAction::MESSAGE]):
                return $this->buildMessageEvent($data);

            case isset($data['action'][AmoJoHookAction::REACTION]):
                return $this->buildReactionEvent($data);

            case isset($data['action'][AmoJoHookAction::TYPING]):
                return $this->buildTypingEvent($data);

            default:
                throw new UnsupportedMessageTypeException('Unknown webhook type');
        }
    }

    private function buildMessageEvent(array $data): AmoJoHookOutgoingMessage
    {
        $msgData = $data[AmoJoHookAction::MESSAGE];
        $message = $msgData[AmoJoHookAction::MESSAGE];
        $replyTo = null;

        if (isset($message['reply_to']) && is_array($message['reply_to'])) {
            $replyTo = (new ReplyTo())
                ->setReplyRefUuid($message['reply_to'][AmoJoHookAction::MESSAGE]['id'])
                ->setReplyUuid($message['reply_to'][AmoJoHookAction::MESSAGE]['msgid']);
        }

        return new AmoJoHookOutgoingMessage(
            $data['account_id'],
            $data['time'] ?? time(),
            $this->buildUser($msgData['receiver'], Receiver::class),
            $this->buildUser($msgData['sender'], Sender::class),
            $msgData['source']['external_id'] ?? null,
            $this->buildConversation($msgData['conversation']),
            (int)$msgData['timestamp'],
            (int)($msgData['msec_timestamp'] ?? time() * 1000),
            $this->messageFactory->create($msgData),
            $replyTo
        );
    }

    private function buildReactionEvent(array $data): AmoJoHookReaction
    {
        $reactionData = $data['action'][AmoJoHookAction::REACTION];

        return new AmoJoHookReaction(
            $data['account_id'],
            $data['time'] ?? time(),
            $this->messageFactory->create($reactionData),
            $this->buildUser($reactionData['user'], Sender::class),
            $this->buildConversation($reactionData['conversation']),
            $reactionData['type'],
            $reactionData['emoji'] ?? null
        );
    }

    private function buildTypingEvent(array $data): AmoJoHookTyping
    {
        $typingData = $data['action'][AmoJoHookAction::TYPING];

        return new AmoJoHookTyping(
            $data['account_id'],
            $data['time'] ?? time(),
            $this->buildUser($typingData['user'], Sender::class),
            $this->buildConversation($typingData['conversation']),
            $typingData['expired_at']
        );
    }
}
