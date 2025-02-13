<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * DTO ответа при выполнении запроса на доставку сообщения
 *
 * @extends AbstractResponse
 */
final class DeliveryResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function getDelivery(): bool
    {
        return true;
    }
}
