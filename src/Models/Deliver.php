<?php

declare(strict_types=1);

namespace AmoJo\Models;

use AmoJo\Enum\DeliveryStatus;
use AmoJo\Enum\ErrorCode;
use AmoJo\Exception\EmptyMessageErrorException;

/**
 * Объект доставки сообщения
 */
class Deliver
{
    /**
     * @see DeliveryStatus
     * @var int обязательное свойство
     */
    private int $status;

    /** @var int|null */
    private ?int $errorCode = null;

    /** @var string|null */
    private ?string $messageError = null;

    /**
     * @param int $status
     */
    public function __construct(int $status)
    {
        $this->status = $status;
    }

    /**
     * Получения cтатуса
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Установка типа ошибки
     *
     * @param int $errorCode
     * @return Deliver
     */
    public function setErrorCode(int $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * Получения типа ошибки
     *
     * @see ErrorCode
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Установка сообщения ошибки
     *
     * @param string $messageError
     * @return $this
     */
    public function setMessageError(string $messageError): self
    {
        $this->messageError = $messageError;
        return $this;
    }

    /**
     * Получения сообщения ошибки
     *
     * @return string|null
     */
    public function getMessageError(): ?string
    {
        return $this->messageError;
    }

    /**
     * Валидирует объект. С передачей типа ошибки 905 требуются передать сообщение
     *
     * @return void
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
            'msgid'           => $msgRefId,
            'delivery_status' => $this->getStatus(),
            'error_code'      => $this->getErrorCode(),
            'error'           => $this->getMessageError(),
        ]);
    }
}
