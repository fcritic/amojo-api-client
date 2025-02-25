<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Enum\WebHookType;
use AmoJo\Exception\InvalidRequestWebHookException;

trait ValidationTrait
{
    /**
     * Валидация вебхука на обязательные параметры
     *
     * @param array $data
     * @param array $requiredFields
     * @param string $errorPrefix
     * @return void
     */
    protected function validateStructure(array $data, array $requiredFields, string $errorPrefix = ''): void
    {
        foreach ($requiredFields as $field) {
            if (!$this->hasField($data, $field)) {
                throw new InvalidRequestWebHookException(
                    "{$errorPrefix} Missing required field: {$field}"
                );
            }
        }
    }

    /**
     * Обязательные свойства для вебхука, в зависимости от его типа
     *
     * @param string $type
     * @return array|string[]
     */
    private function getValidationRules(string $type): array
    {
        switch ($type) {
            case WebHookType::MESSAGE:
                return [
                    'account_id',
                    'message.conversation.id',
                    'message.sender.id',
                    'message.receiver.id',
                    'message.message.id',
                    'message.message.type',
                    'message.timestamp'
                ];
            case WebHookType::REACTION:
                return [
                    'account_id',
                    'action.reaction.msgid',
                    'action.reaction.user.id',
                    'action.reaction.conversation.id',
                    'action.reaction.type'
                ];
            case WebHookType::TYPING:
                return [
                    'account_id',
                    'action.typing.expired_at',
                    'action.typing.user.id',
                    'action.typing.conversation.id'
                ];
            default:
                return [];
        }
    }

    /**
     * @param array $data
     * @param string $field
     * @return bool
     */
    private function hasField(array $data, string $field): bool
    {
        $keys = explode('.', $field);
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
            $data = $data[$key];
        }
        return true;
    }
}
