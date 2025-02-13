<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * DTO ответа на запрос реакции
 *
 * @extends AbstractResponse
 */
final class ReactResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function getReact(): bool
    {
        return true;
    }
}
