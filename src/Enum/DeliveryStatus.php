<?php

declare(strict_types=1);

namespace AmoJo\Enum;

use AmoJo\Models\Deliver;

/**
 * Доступные статусы о доставки
 * @see Deliver
 */
class DeliveryStatus
{
    /** @var string Отправлено */
    public const SENT = '';

    /** @var int Доставлено */
    public const DELIVERED = 1;

    /** @var int Прочитано */
    public const READ = 2;

    /** @var int Ошибка */
    public const ERROR = -1;
}
