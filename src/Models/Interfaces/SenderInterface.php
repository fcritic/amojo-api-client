<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

interface SenderInterface extends UserInterface
{
    public function toTyping(): array;
    public function toReact(): ?array;
}
