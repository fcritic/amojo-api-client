<?php

declare(strict_types=1);

namespace AmoJo\Models\Users\ValueObject;

use function array_filter;

/**
 * Профиль участника чата
 */
class UserProfile
{
    private ?string $phone = null;
    private ?string $email = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function toPayload(): ?array
    {
        return array_filter([
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
        ]) ?: null;
    }
}
