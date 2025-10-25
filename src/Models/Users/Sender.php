<?php

declare(strict_types=1);

namespace AmoJo\Models\Users;

use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Interfaces\SenderInterface;

/**
 * Объект отправителя. При входящим сообщение принимает uid пользователя amoCRM
 *
 * Данный объект используется как контакт(user) при создании чата и других действий, где требуется объект User
 */
final class Sender extends AbstractUser implements SenderInterface
{
    protected function validateToReact(): void
    {
        if ($this->getId() === null && $this->getRefId() === null) {
            throw new AmoJoException();
        }
    }

    public function toTyping(): array
    {
        return ['id' => $this->getId()];
    }

    public function toReact(): ?array
    {
        $this->validateToReact();

        return array_filter([
            'id' => $this->getId(),
            'ref_id' => $this->getRefId(),
        ]);
    }
}
