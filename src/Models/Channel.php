<?php

declare(strict_types=1);

namespace AmoJo\Models;

/**
 * Объект канала чатов. Обязательный параметр для клиента
 * @see AmoJoClient
 */
class Channel
{
    /** id канала чатов. Выдается при регистрации канал чатов */
    private string $uuid;
    /** secret_key канала чатов. Выдается при регистрации канала чатов */
    private string $secretKey;

    public function __construct(string $uuid, string $secretKey)
    {
        $this->uuid = $uuid;
        $this->secretKey = $secretKey;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
