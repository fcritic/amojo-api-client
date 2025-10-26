<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Enum\AmoJoHookAction;
use AmoJo\Exception\InvalidRequestWebHookException;

trait ValidationTrait
{
    protected function validateStructure(array $data, array $requiredFields, string $errorPrefix = ''): void
    {
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!$this->hasField($data, $field)) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new InvalidRequestWebHookException(
                sprintf('[%s] Missing fields: %s', $errorPrefix, implode(', ', $missing))
            );
        }
    }

    private function getValidationRules(string $type): array
    {
        static $rules = [
            AmoJoHookAction::MESSAGE => [
                'account_id',
                'time',
                'message.sender.id',
                'message.receiver.id',
                'message.conversation.id',
                'message.timestamp',
                'message.msec_timestamp',
                'message.message.id',
                'message.message.type',
            ],
            AmoJoHookAction::REACTION => [
                'account_id',
                'time',
                'action.reaction.msgid',
                'action.reaction.user.id',
                'action.reaction.conversation.id',
                'action.reaction.type',
            ],
            AmoJoHookAction::TYPING =>  [
                'account_id',
                'time',
                'action.typing.user.id',
                'action.typing.conversation.id',
                'action.typing.expired_at',
            ],
        ];

        return $rules[$type] ?? [];
    }

    private function hasField(array $data, string $field): bool
    {
        // Статический кэш для хранения разбитых путей
        static $pathCache = [];

        // Если путь не закэширован - разбиваем и сохраняем
        if (!isset($pathCache[$field])) {
            $pathCache[$field] = explode('.', $field);
        }

        // Работаем с исходными данными через ссылку
        $current = &$data;

        // Итерация по закэшированному пути
        foreach ($pathCache[$field] as $key) {
            if (!isset($current[$key])) {
                return false;
            }
            // Перемещаемся вглубь массива по ссылке
            $current = &$current[$key];
        }
        return true;
    }
}
