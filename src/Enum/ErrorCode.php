<?php

declare(strict_types=1);

namespace AmoJo\Enum;

use AmoJo\Models\Deliver;

/**
 * Доступные коды ошибки для доставки сообщения
 * @see Deliver
 */
class ErrorCode
{
    /** @var int Пользователь удалил переписку */
    public const CONVERSATION_DELETED = 901;

    /** @var int Интеграция отключена на стороне канала */
    public const INTEGRATION_DISABLED = 902;

    /** @var int Внутрення ошибка сервера */
    public const INTERNAL_SERVER = 903;

    /** @var int Невозможно создать переписку (Например, пользователь не зарегистрирован в WhatsApp) */
    public const CONVERSATION_CREATION_FAILED = 904;

    /** @var int Любая другая, вместе с данным кодом ошибки необходимо передать текст ошибки */
    public const WITH_DESCRIPTION = 905;
}
