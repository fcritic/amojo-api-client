<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

abstract class AbstractAmoJoHook implements AmoJoHookInterface
{
    protected string $accountUuid;
    protected int $time;
    protected Conversation $conversation;
    protected UserInterface $initiator;

    public function __construct(
        string $accountUuid,
        int $time,
        UserInterface $initiator,
        Conversation $conversation
    ) {
        $this->accountUuid = $accountUuid;
        $this->time = $time;
        $this->initiator = $initiator;
        $this->conversation = $conversation;
    }

    abstract public function getType(): string;

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function getAccountUuid(): string
    {
        return $this->accountUuid;
    }

    /**
     * Возвращает юзера. Для вебхука типа: печатает, реакция
     */
    public function getInitiator(): UserInterface
    {
        return $this->initiator;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function toArray(): array
    {
        return [
            'account_id' => $this->getAccountUuid(),
            'time' => $this->getTime()
        ];
    }
}
