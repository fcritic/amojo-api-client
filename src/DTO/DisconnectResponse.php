<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * DTO ответа при отключении канала чатов от аккаунта
 *
 * @extends AbstractResponse
 */
final class DisconnectResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function getDisconnect(): bool
    {
        return true;
    }
}
