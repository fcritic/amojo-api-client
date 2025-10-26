<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

interface MessageInterface
{
    public function setUuid(string $uid): self;
    public function getUuid(): ?string;
    public function setRefUuid(string $refUid): self;
    public function getRefUuid(): ?string;
    public function getType(): ?string;
    public function setText(string $text): self;
    public function getText(): ?string;
    public function setTimestamp(int $timestamp): self;
    public function getTimestamp(): int;
    public function setMsecTimestamp(int $timestamp): self;
    public function getMsecTimestamp(): int;
    public function toPayload(): array;
}
