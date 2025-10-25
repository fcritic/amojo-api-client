<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Exception\RequiredParametersMissingException;

class Conversation
{
    private ?string $id = null;
    private ?string $refId = null;

    public function getId(): ?string
    {
        $this->validate();

        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRefId(): ?string
    {
        $this->validate();

        return $this->refId;
    }

    /**
     * Установка идентификатора чата на стороне API чатов, необязательное поле.
     * Необходимо передавать, когда клиент ответит на сообщение отправленное с помощью "Написать первым",
     * чтобы API чатов связало чат на вашей стороне с чатом в системе
     */
    public function setRefId(string $refId): self
    {
        $this->refId = $refId;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'ref_id' => $this->getRefId(),
        ];
    }

    private function validate(): void
    {
        if ($this->id === null && $this->refId === null) {
            throw new RequiredParametersMissingException('Required parameter "id" or "refId" missing.');
        }
    }
}
