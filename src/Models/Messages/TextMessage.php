<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;

/**
 * @extends AbstractMessage
 */
final class TextMessage extends AbstractMessage
{
    /** @var string */
    protected const TYPE = MessageType::TEXT;
}
