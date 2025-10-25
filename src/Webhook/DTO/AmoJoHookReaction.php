<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Enum\AmoJoHookAction;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\UserInterface;

final class AmoJoHookReaction extends AbstractAmoJoHook
{
    protected MessageInterface $message;
    protected string $reactionType;
    protected ?string $emoji = null;

    public function __construct(
        string $accountUuid,
        int $time,
        MessageInterface $message,
        UserInterface $user,
        Conversation $conversation,
        string $reactionType,
        ?string $emoji
    ) {
        $this->message = $message;
        $this->reactionType = $reactionType;
        $this->emoji = $emoji;

        parent::__construct($accountUuid, $time, $user, $conversation);
    }

    public function getType(): string
    {
        return AmoJoHookAction::REACTION;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getReactionType(): string
    {
        return $this->reactionType;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'action' => [
                $this->getType() => [
                    'message' => $this->getMessage()->toArrayForWebHook(),
                    'user' => $this->getInitiator()->toPayload(),
                    'conversation' => $this->getConversation()->toArray(),
                    'type' => $this->getReactionType(),
                    'emoji' => $this->getEmoji(),
                ],
            ]
        ];
    }
}
