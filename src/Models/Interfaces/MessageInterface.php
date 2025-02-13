<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

/**
 * Интерфейс сообщения
 */
interface MessageInterface
{
    /**
     * @param string $uid
     * @return self
     */
    public function setUid(string $uid): self;

    /**
     * @return string|null
     */
    public function getUid(): ?string;

    /**
     * @param string $refUid
     * @return self
     */
    public function setRefUid(string $refUid): self;

    /**
     * @return string|null
     */
    public function getRefUid(): ?string;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self;

    /**
     * @return string|null
     */
    public function getText(): ?string;

    /**
     * @param int $timestamp
     * @return self
     */
    public function setTimestamp(int $timestamp): self;

    /**
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * @param int $timestamp
     * @return self
     */
    public function setMsecTimestamp(int $timestamp): self;

    /**
     * @return int
     */
    public function getMsecTimestamp(): int;

    /**
     * @return array
     */
    public function toPayload(): array;
}
