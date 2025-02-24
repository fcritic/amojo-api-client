<?php

declare(strict_types=1);

use AmoJo\Models\Conversation;

trait ConversationParserTrait
{
    protected function parseConversation(array $data): Conversation
    {
        return (new Conversation())
            ->setRefId($data['id'])
            ->setId($data['client_id'] ?? '');
    }
}
