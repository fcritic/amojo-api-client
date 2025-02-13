<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * DTO ответа при выполнении запроса на печатания
 *
 * @extends AbstractResponse
 */
final class TypingResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function getTyping(): bool
    {
        return true;
    }
}
