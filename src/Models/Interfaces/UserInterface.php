<?php

declare(strict_types=1);

namespace AmoJo\Models\Interfaces;

use AmoJo\Models\Users\ValueObject\UserProfile;

/**
 * Интерфейс пользователя. Его реализуют Получатель и Отправитель
 */
interface UserInterface
{
    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @param string $id
     * @return self
     */
    public function setId(string $id): self;

    /**
     * @return string|null
     */
    public function getRefId(): ?string;

    /**
     * @param string $refId
     * @return self
     */
    public function setRefId(string $refId): self;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * @return string|null
     */
    public function getAvatar(): ?string;

    /**
     * @param string $avatar
     * @return self
     */
    public function setAvatar(string $avatar): self;

    /**
     * @return UserProfile|null
     */
    public function getProfile(): ?UserProfile;

    /**
     * @param UserProfile $profile
     * @return self
     */
    public function setProfile(UserProfile $profile): self;

    /**
     * @return string|null
     */
    public function getProfileLink(): ?string;

    /**
     * @param string $profileLink
     * @return self
     */
    public function setProfileLink(string $profileLink): self;

    /**
     * @return array
     */
    public function toPayload(): array;
}
