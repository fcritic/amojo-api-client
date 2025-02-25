<?php

declare(strict_types=1);

namespace AmoJo\Enum;

class WebHookType
{
    /** @var string вебхук исходящего сообщения */
    public const MESSAGE = 'message';

    /** @var string вебхук реакции */
    public const REACTION = 'reaction';

    /** @var string вебхук печатания */
    public const TYPING = 'typing';
}
