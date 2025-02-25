<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Exception\RequiredParametersMissingException;

/**
 * Объект чата
 */
class Conversation
{
    /** @var string|null */
    private ?string $id = null;

    /** @var string|null */
    private ?string $refId = null;

    /**
     * Получения идентификатора чата на стороне интеграции
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        $this->validate();
        return $this->id;
    }

    /**
     * Установка идентификатора чата на стороне интеграции
     *
     * @param string $id
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получения идентификатора чата на стороне API чатов
     *
     * @return string|null
     */
    public function getRefId(): ?string
    {
        $this->validate();
        return $this->refId;
    }

    /**
     * Установка идентификатора чата на стороне API чатов, необязательное поле.
     * Необходимо передавать, когда клиент ответит на сообщение отправленное с помощью "Написать первым",
     * чтобы API чатов связало чат на вашей стороне с чатом в системе
     *
     * @param string $refId
     * @return $this
     */
    public function setRefId(string $refId): self
    {
        $this->refId = $refId;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'ref_id' => $this->getRefId(),
        ];
    }

    /**
     * Валидация чата. При получении данных с объекта, он должен иметь хоть одно значения
     *
     * @return void
     */
    private function validate(): void
    {
        if ($this->id === null && $this->refId === null) {
            throw new RequiredParametersMissingException('Required parameter "id" or "refId" missing.');
        }
    }
}
