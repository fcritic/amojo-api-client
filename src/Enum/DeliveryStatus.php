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
    /** Отправлено */
    public const SENT = '';
    /** Доставлено */
    public const DELIVERED = 1;
    /** Прочитано */
    public const READ = 2;
    /** Ошибка */
    public const ERROR = -1;
}
