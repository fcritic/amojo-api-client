<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Models\Conversation;

trait ConversationParserTrait
{
    /**
     * Создания чата для вебхука
     *
     * @param array $data
     * @return Conversation
     */
    protected function parseConversation(array $data): Conversation
    {
        return (new Conversation())
            ->setRefId($data['id'])
            ->setId($data['client_id'] ?? '');
    }
}
