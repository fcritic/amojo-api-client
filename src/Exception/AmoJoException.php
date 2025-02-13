<?php

declare(strict_types=1);

namespace AmoJo\Exception;

use RuntimeException;
use Throwable;

/**
 * Основное исключение
 */
class AmoJoException extends RuntimeException
{
    /** @var string|null */
    protected ?string $type;

    /** @var array|null */
    protected ?array $context;

    public function __construct(
        string $message = '',
        int $code = 400,
        string $type = null,
        array $context = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->type = $type;
        $this->context = $context;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return array|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }
}
