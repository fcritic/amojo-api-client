<?php

declare(strict_types=1);

namespace AmoJo\Exception;

use RuntimeException;
use Throwable;

class AmoJoException extends RuntimeException
{
    protected ?string $type;
    protected ?array $context;

    public function __construct(
        string $message = '',
        int $code = 400,
        string $type = null,
        array $context = null,
        ?Throwable $previous = null
    ) {
        $this->type = $type;
        $this->context = $context;

        parent::__construct($message, $code, $previous);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }
}
