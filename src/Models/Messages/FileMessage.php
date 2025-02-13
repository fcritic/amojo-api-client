<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;

/**
 * @extends AbstractMessage
 */
final class FileMessage extends AbstractMessage
{
    /** @var string */
    protected const TYPE = MessageType::FILE;
}
