<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

/**
 * Объект цитаты c ответом. При редактировании сообщения поле будет пригнорировано.
 */
final class ReplyTo
{
    /** @var string|null */
    private ?string $replyUid = null;

    /** @var string|null */
    private ?string $replyRefUid = null;

    /**
     * Получения идентификатора сообщения на стороне интеграции
     *
     * @return string|null
     */
    public function getReplyUid(): ?string
    {
        return $this->replyUid;
    }

    /**
     * Установка идентификатора сообщения на стороне интеграции
     *
     * @param string|null $replyUid
     * @return ReplyTo
     */
    public function setReplyUid(?string $replyUid): self
    {
        $this->replyUid = $replyUid;
        return $this;
    }

    /**
     * Получения идентификатора сообщения в API чатов
     *
     * @return string|null
     */
    public function getReplyRefUid(): ?string
    {
        return $this->replyRefUid;
    }

    /**
     * Получения идентификатора сообщения в API чатов
     *
     * @param string|null $replyRefUid
     * @return ReplyTo
     */
    public function setReplyRefUid(?string $replyRefUid): self
    {
        $this->replyRefUid = $replyRefUid;
        return $this;
    }

    /**
     * Возвращает массив message для объекта Payload
     *
     * @return array|null
     */
    public function toPayload(): ?array
    {
        $message = array_filter([
            'id'    => $this->getReplyRefUid(),
            'msgid' => $this->getReplyUid(),
        ]);

        return ['message' => $message];
    }
}
