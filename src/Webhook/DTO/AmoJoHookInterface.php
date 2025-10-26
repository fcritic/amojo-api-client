<?php

declare(strict_types=1);

namespace AmoJo\Webhook\DTO;

use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\UserInterface;

interface AmoJoHookInterface
{
    public function getConversation(): Conversation;
    public function getAccountUuid(): string;
    public function getInitiator(): UserInterface;
    public function getTime(): int;
    public function toArray(): array;
}
