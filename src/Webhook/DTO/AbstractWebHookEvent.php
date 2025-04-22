<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

abstract class AbstractWebHookEvent implements DtoInterface
{
    /** @var string */
    protected string $accountUid;

    /** @var int */
    protected int $time;

    /** @var Conversation */
    protected Conversation $conversation;

    /** @var UserInterface */
    protected UserInterface $initiator;

    /**
     * @param string $accountUid
     * @param int $time
     * @param UserInterface $initiator
     * @param Conversation $conversation
     */
    public function __construct(
        string $accountUid,
        int $time,
        UserInterface $initiator,
        Conversation $conversation
    ) {
        $this->accountUid = $accountUid;
        $this->time = $time;
        $this->initiator = $initiator;
        $this->conversation = $conversation;
    }

    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    /**
     * @return string
     */
    public function getAccountUid(): string
    {
        return $this->accountUid;
    }

    /**
     * Возвращает юзера. Для вебхука типа: печатает, реакция
     *
     * @return UserInterface
     */
    public function getInitiator(): UserInterface
    {
        return $this->initiator;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'accountUid' => $this->getAccountUid(),
            'time' => $this->getTime()
        ];
    }
}
