<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;

final class TextMessage extends AbstractMessage
{
    protected const TYPE = MessageType::TEXT;
}
