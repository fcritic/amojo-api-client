<?php

declare(strict_types=1);

namespace AmoJo\Enum;

/**
 * Передаваемые заголовки в каждом запросе
 */
class HeaderType
{
    public const DATE = 'Date';
    public const CONTENT_TYPE = 'Content-Type';
    public const CONTENT_MD5 = 'Content-MD5';
    public const SIGNATURE = 'X-Signature';
    public const USER_AGENT = 'User-Agent';
}
