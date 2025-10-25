<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Enum\AmoJoHookAction;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

final class AmoJoHookTyping extends AbstractAmoJoHook
{
    protected int $expiredAt;

    public function __construct(
        string $accountUuid,
        int $time,
        UserInterface $user,
        Conversation $conversation,
        int $expiredAt
    ) {
        $this->expiredAt = $expiredAt;

        parent::__construct($accountUuid, $time, $user, $conversation);
    }

    public function getType(): string
    {
        return AmoJoHookAction::TYPING;
    }

    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'action' => [
                $this->getType() => [
                    'user' => $this->getInitiator()->toPayload(),
                    'conversation' => $this->getConversation()->toArray(),
                    'expired_at' => $this->getExpiredAt()
                ]
            ]
        ];
    }
}
