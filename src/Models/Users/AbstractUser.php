<?php

declare(strict_types=1);

namespace AmoJo\Models\Users;

use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\ValueObject\UserProfile;

use function array_filter;

abstract class AbstractUser implements UserInterface
{
    protected ?string $id = null;
    protected ?string $refId = null;
    protected ?string $name = null;
    protected ?string $avatar = null;
    protected ?string $profileLink = null;
    protected ?UserProfile $profile = null;

    /**
     * Получения идентификатора участника чата на стороне интеграции
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Установка идентификатора участника чата на стороне интеграции
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Получения идентификатора участника чата на стороне API Чатов
     */
    public function getRefId(): ?string
    {
        return $this->refId;
    }

    /**
     * Установка идентификатора участника чата на стороне API Чатов
     */
    public function setRefId(string $refId): self
    {
        $this->refId = $refId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    public function setProfile(UserProfile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getProfileLink(): ?string
    {
        return $this->profileLink;
    }

    public function setProfileLink(string $profileLink): self
    {
        $this->profileLink = $profileLink;

        return $this;
    }

    protected function validateUser(): void
    {
        if ($this->getId() === null && $this->getRefId() === null) {
            throw new AmoJoException('The ID or Ref ID parameter is mandatory for the chat participant.');
        }
    }

    public function toPayload(): array
    {
        $this->validateUser();

        return array_filter([
            'id' => $this->getId(),
            'ref_id' => $this->getRefId(),
            'name' => $this->getName(),
            'avatar' => $this->getAvatar(),
            'profile_link' => $this->getProfileLink(),
            'profile' => $this->getProfile() ? $this->getProfile()->toPayload() : null,
        ]);
    }
}
