<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Enum\DeliveryStatus;
use AmoJo\Enum\ErrorCode;
use AmoJo\Exception\EmptyMessageErrorException;

class Deliver
{
    private int $status;
    private ?int $errorCode = null;
    private ?string $messageError = null;

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setErrorCode(int $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function setMessageError(string $messageError): self
    {
        $this->messageError = $messageError;

        return $this;
    }

    public function getMessageError(): ?string
    {
        return $this->messageError;
    }

    /**
     * Валидирует объект. С передачей типа ошибки 905 требуются передать сообщение
     */
    private function validate(): void
    {
        if ($this->getErrorCode() === ErrorCode::WITH_DESCRIPTION && $this->getMessageError() === null) {
            throw new EmptyMessageErrorException('An error message is required for the 905 code.');
        }
    }

    /**
     * Отдает массив для выполнения запроса обновления статуса
     *
     * @param string $msgRefId ID сообщения в API чатов
     * @return array
     */
    public function toApi(string $msgRefId): array
    {
        $this->validate();

        return array_filter([
            'msgid' => $msgRefId,
            'delivery_status' => $this->getStatus(),
            'error_code' => $this->getErrorCode(),
            'error' => $this->getMessageError(),
        ]);
    }
}
