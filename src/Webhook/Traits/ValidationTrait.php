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
        $missing = []; // Собираем все ошибки

        foreach ($requiredFields as $field) {
            if (!$this->hasField($data, $field)) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new InvalidRequestWebHookException(
                $errorPrefix . " Missing fields: " . implode(', ', $missing)
            );
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
        static $rules = [
            WebHookType::MESSAGE => [
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
            WebHookType::REACTION => [
                'account_id',
                'time',
                'action.reaction.msgid',
                'action.reaction.user.id',
                'action.reaction.conversation.id',
                'action.reaction.type',
            ],
            WebHookType::TYPING =>  [
                'account_id',
                'time',
                'action.typing.user.id',
                'action.typing.conversation.id',
                'action.typing.expired_at',
            ],
        ];

        return $rules[$type] ?? [];
    }

    /**
     * @param array $data
     * @param string $field
     * @return bool
     */
    private function hasField(array $data, string $field): bool
    {
        // Статический кэш для хранения разбитых путей
        static $pathCache = [];

        // Если путь не закэширован — разбиваем и сохраняем
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
