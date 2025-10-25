<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Traits\ContentTrait;
use AmoJo\Models\Traits\MediaTrait;

use function time;

abstract class AbstractMessage implements MessageInterface
{
    use MediaTrait;
    use ContentTrait;

    protected const TYPE = '';

    private ?string $uuid = null;
    private ?string $refUuid = null;
    private ?string $text = null;
    private int $msecTimestamp;
    private int $timestamp;

    /**
     * Возвращает тип сообщения.
     * @see MessageType
     */
    public function getType(): string
    {
        return static::TYPE;
    }

    /**
     * Установка идентификатора сообщения чата на стороне интеграции.
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Получения идентификатора сообщения чата на стороне интеграции.
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Установка идентификатора сообщения чата в API чатов.
     */
    public function setRefUuid(string $refUid): self
    {
        $this->refUuid = $refUid;

        return $this;
    }

    /**
     * Получение идентификатора сообщения чата в API чатов.
     */
    public function getRefUuid(): ?string
    {
        return $this->refUuid;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp ?? time();
    }

    public function setMsecTimestamp(int $timestamp): self
    {
        $this->msecTimestamp = $timestamp;

        return $this;
    }

    public function getMsecTimestamp(): int
    {
        return $this->msecTimestamp ?? $this->getTimestamp() * 1000;
    }

    private function validate(): void
    {
        if ($this->uuid === null) {
            throw new RequiredParametersMissingException('Required param uid message missing.');
        }
    }

    /**
     * Для вебхука по реакции
     */
    public function toArrayForWebHook(): array
    {
        return [
            'id' => $this->getUuid(),
            'client_id' => $this->getRefUuid(),
            'timestamp' => $this->getTimestamp(),
            'msec_timestamp' => $this->getMsecTimestamp(),
        ];
    }

    /**
     * Возвращает массив message для объекта Payload
     */
    public function toPayload(): array
    {
        $this->validate();

        return array_filter([
            'type' => $this->getType(),
            'text' => $this->getText(),
            'media' => $this->getMedia(),
            'file_name' => $this->getFileName(),
            'file_size' => $this->getFileSize(),
            'media_duration' => $this->getMediaDuration(),
        ]);
    }
}
