<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Enum\WebHookType;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

/**
 * События печатания
 *
 * @extends AbstractWebHookEvent
 */
final class TypingEvent extends AbstractWebHookEvent
{
    /** @var int */
    protected int $expiredAt;

    /**
     * @param string $accountUid
     * @param int $time
     * @param UserInterface $user
     * @param Conversation $conversation
     * @param int $expiredAt
     */
    public function __construct(
        string $accountUid,
        int $time,
        UserInterface $user,
        Conversation $conversation,
        int $expiredAt
    ) {
        parent::__construct($accountUid, $time, $user, $conversation);

        $this->expiredAt = $expiredAt;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return WebHookType::TYPING;
    }

    /**
     * @return int
     */
    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    /**
     * @return array
     */
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
