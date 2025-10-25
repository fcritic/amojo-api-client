<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

/**
 * Объект цитаты c ответом. При редактировании сообщения поле будет пригнорировано.
 */
final class ReplyTo
{
    private ?string $replyUuid = null;
    private ?string $replyRefUuid = null;

    /**
     * Получения идентификатора сообщения на стороне интеграции
     */
    public function getReplyUuid(): ?string
    {
        return $this->replyUuid;
    }

    /**
     * Установка идентификатора сообщения на стороне интеграции
     */
    public function setReplyUuid(?string $replyUuid): self
    {
        $this->replyUuid = $replyUuid;

        return $this;
    }

    /**
     * Получения идентификатора сообщения в API чатов
     */
    public function getReplyRefUuid(): ?string
    {
        return $this->replyRefUuid;
    }

    /**
     * Получения идентификатора сообщения в API чатов
     */
    public function setReplyRefUuid(?string $replyRefUuid): self
    {
        $this->replyRefUuid = $replyRefUuid;

        return $this;
    }

    /**
     * Возвращает массив message для объекта Payload
     */
    public function toPayload(): ?array
    {
        $message = array_filter([
            'id' => $this->getReplyRefUuid(),
            'msgid' => $this->getReplyUuid(),
        ]);

        return ['message' => $message];
    }
}
