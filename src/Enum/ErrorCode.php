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
    /** Пользователь удалил переписку */
    public const CONVERSATION_DELETED = 901;
    /** Интеграция отключена на стороне канала */
    public const INTEGRATION_DISABLED = 902;
    /** Внутрення ошибка сервера */
    public const INTERNAL_SERVER = 903;
    /** Невозможно создать переписку (Например, пользователь не зарегистрирован в WhatsApp) */
    public const CONVERSATION_CREATION_FAILED = 904;
    /** Любая другая, вместе с данным кодом ошибки необходимо передать текст ошибки */
    public const WITH_DESCRIPTION = 905;
}
