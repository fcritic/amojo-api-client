<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Передаваемые заголовки в каждом запросе
 */
class HeaderType
{
    /** @var string */
    public const DATE = 'Date';

    /** @var string */
    public const CONTENT_TYPE = 'Content-Type';

    /** @var string */
    public const CONTENT_MD5 = 'Content-MD5';

    /** @var string */
    public const SIGNATURE = 'X-Signature';

    /** @var string */
    public const USER_AGENT = 'User-Agent';
}
