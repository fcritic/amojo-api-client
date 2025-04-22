<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

interface DtoInterface
{
    /**
     * @return Conversation
     */
    public function getConversation(): Conversation;

    /**
     * @return string
     */
    public function getAccountUid(): string;

    /**
     * @return UserInterface
     */
    public function getInitiator(): UserInterface;

    /**
     * @return int
     */
    public function getTime(): int;

    /**
     * @return array
     */
    public function toArray(): array;
}
