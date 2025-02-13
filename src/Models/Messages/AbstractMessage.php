<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;
use AmoJo\Middleware\MiddlewareInterface;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Traits\ContentTrait;
use AmoJo\Models\Traits\MediaTrait;

use function time;

/**
 * @implements MiddlewareInterface
 */
abstract class AbstractMessage implements MessageInterface
{
    use MediaTrait;
    use ContentTrait;

    /** @var string|null */
    private ?string $uid = null;

    /** @var string|null */
    private ?string $refUid = null;

    /** @var string|null */
    private ?string $text = null;

    /** @var int */
    private int $msecTimestamp;

    /** @var int */
    private int $timestamp;

    /**
     * Возвращает тип сообщения.
     *
     * @see MessageType
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE;
    }

    /**
     * Установка идентификатора сообщения чата на стороне интеграции.
     *
     * @param string $uid
     * @return $this
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Получения идентификатора сообщения чата на стороне интеграции.
     *
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * Установка идентификатора сообщения чата в API чатов.
     *
     * @param string $refUid
     * @return $this
     */
    public function setRefUid(string $refUid): self
    {
        $this->refUid = $refUid;
        return $this;
    }

    /**
     * Получение идентификатора сообщения чата в API чатов.
     *
     * @return string|null
     */
    public function getRefUid(): ?string
    {
        return $this->refUid;
    }

    /**
     * Установка текста. Любое сообщения вне зависимости от его типа может иметь текст
     *
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Получения текста. Любое сообщения вне зависимости от его типа может иметь текст
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Установка времени сообщения, метка unix
     *
     * @param int $timestamp
     * @return $this
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Получения времени сообщения, метка unix
     *
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp ?? time();
    }

    /**
     * Установка времени сообщения в миллисекундах
     *
     * @param int $timestamp
     * @return $this
     */
    public function setMsecTimestamp(int $timestamp): self
    {
        $this->msecTimestamp = $timestamp;
        return $this;
    }

    /**
     * Получения времени сообщения в миллисекундах
     *
     * @return int
     */
    public function getMsecTimestamp(): int
    {
        return $this->msecTimestamp ?? $this->getTimestamp() * 1000;
    }

    /**
     * Валидация сообщения по обязательным полям
     *
     * @return void
     */
    private function validate(): void
    {
        if ($this->uid === null) {
            throw new RequiredParametersMissingException('Required param uid message missing.');
        }
    }

    /**
     * Возвращает массив message для объекта Payload
     *
     * @return array
     */
    public function toPayload(): array
    {
        $this->validate();

        return array_filter([
            'type'           => $this->getType(),
            'text'           => $this->getText(),
            'media'          => $this->getMedia(),
            'file_name'      => $this->getFileName(),
            'file_size'      => $this->getFileSize(),
            'media_duration' => $this->getMediaDuration(),
        ]);
    }
}
