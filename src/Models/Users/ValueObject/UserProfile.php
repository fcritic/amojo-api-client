<?php

declare(strict_types=1);

namespace AmoJo\Models\Users\ValueObject;

use function array_filter;

/**
 * Профиль участника чата
 */
class UserProfile
{
    /** @var string|null */
    private ?string $phone = null;

    /** @var string|null */
    private ?string $email = null;

    /**
     * Получение телефона участника чата
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Установка телефона участника чата
     *
     * @param string $phone
     * @return UserProfile
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Получение email`a участника чата
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Установка email`a участника чата
     *
     * @param string $email
     * @return UserProfile
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Возвращает массив profile для объекта Sender/Receiver
     *
     * @return array|null
     */
    public function toPayload(): ?array
    {
        return array_filter([
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
        ]) ?: null;
    }
}
