<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;

final class FileMessage extends AbstractMessage
{
    protected const TYPE = MessageType::FILE;
}
