<?php

declare(strict_types=1);

namespace AmoJo\Models;

/**
 * Объект канала чатов. Обязательный параметр для клиента
 * @see AmoJoClient
 */
class Channel
{
    /** @var string id канала чатов. Выдается при регистрации канал чатов */
    private string $uid;

    /** @var string secret_key канала чатов. Выдается при регистрации канала чатов */
    private string $secretKey;

    /**
     * @param string $uid
     * @param string $secretKey
     */
    public function __construct(string $uid, string $secretKey)
    {
        $this->uid       = $uid;
        $this->secretKey = $secretKey;
    }

    /**
     * Получения uid канала чатов
     *
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Получения секретного ключа канала чатов
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
