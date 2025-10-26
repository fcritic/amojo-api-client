<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Models\Conversation;

trait ConversationBuilderTrait
{
    protected function buildConversation(array $data): Conversation
    {
        return (new Conversation())
            ->setRefId($data['id'])
            ->setId($data['client_id'] ?? '');
    }
}
