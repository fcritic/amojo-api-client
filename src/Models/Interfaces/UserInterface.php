<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

use AmoJo\Models\Users\ValueObject\UserProfile;

/**
 * Интерфейс пользователя. Его реализуют Получатель и Отправитель
 */
interface UserInterface
{
    public function getId(): ?string;
    public function setId(string $id): self;
    public function getRefId(): ?string;
    public function setRefId(string $refId): self;
    public function getName(): ?string;
    public function setName(string $name): self;
    public function getAvatar(): ?string;
    public function setAvatar(string $avatar): self;
    public function getProfile(): ?UserProfile;
    public function setProfile(UserProfile $profile): self;
    public function getProfileLink(): ?string;
    public function setProfileLink(string $profileLink): self;
    public function toPayload(): array;
}
