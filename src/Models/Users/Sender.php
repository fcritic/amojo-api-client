<?php

declare(strict_types=1);

namespace AmoJo\Models\Users;

use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Interfaces\SenderInterface;

/**
 * Объект отправителя. При входящим сообщение принимает uid пользователя amoCRM
 *
 * Данный объект используется как контакт(user) при создании чата и других действий, где требуется объект User
 *
 * @implements SenderInterface
 * @extends AbstractUser
 */
final class Sender extends AbstractUser implements SenderInterface
{
    /**
     * Валидирует параметры для установки реакции
     *
     * @return void
     */
    protected function validateToReact(): void
    {
        if ($this->getId() === null && $this->getRefId() === null) {
            throw new AmoJoException();
        }
    }
    /**
     * Возвращает массив с ID юзера на стороне интеграции
     *
     * @return array
     */
    public function toTyping(): array
    {
        return [
            'id' => $this->getId()
        ];
    }

    /**
     * Возвращает массив с ID юзера на стороне интеграции и на стороне API чатов. Может быть передан любой параметр
     *
     * @return array|null
     */
    public function toReact(): ?array
    {
        $this->validateToReact();

        return array_filter([
            'id'     => $this->getId(),
            'ref_id' => $this->getRefId(),
        ]);
    }
}
