<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

/**
 * Интерфейс отправителя
 *
 * @extends UserInterface
 */
interface SenderInterface extends UserInterface
{
    /**
     * @return array
     */
    public function toTyping(): array;

    /**
     * @return array
     */
    public function toReact(): ?array;
}
