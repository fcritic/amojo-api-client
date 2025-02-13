<?php

declare(strict_types=1);

namespace AmoJo\Models\Users;

use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\ValueObject\UserProfile;

use function array_filter;

/**
 * @implements UserInterface
 */
abstract class AbstractUser implements UserInterface
{
    /** @var string|null */
    protected ?string $id = null;

    /** @var string|null */
    protected ?string $refId = null;

    /** @var string|null */
    protected ?string $name = null;

    /** @var string|null */
    protected ?string $avatar = null;

    /** @var string|null */
    protected ?string $profileLink = null;

    /** @var UserProfile|null */
    protected ?UserProfile $profile = null;

    /**
     * Получения идентификатора участника чата на стороне интеграции
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Установка идентификатора участника чата на стороне интеграции
     *
     * @param string $id
     * @return AbstractUser
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получения идентификатора участника чата на стороне API Чатов
     *
     * @return string|null
     */
    public function getRefId(): ?string
    {
        return $this->refId;
    }

    /**
     * Установка идентификатора участника чата на стороне API Чатов
     *
     * @param string $refId
     * @return AbstractUser
     */
    public function setRefId(string $refId): self
    {
        $this->refId = $refId;
        return $this;
    }

    /**
     * Получения имя участника чата
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установка имя участника чата
     *
     * @param string $name
     * @return AbstractUser
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получения ссылки на аватара участника чата
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Установка ссылки на аватара участника чата
     *
     * @param string $avatar
     * @return AbstractUser
     */
    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Получения профиля участника чата
     *
     * @return UserProfile|null
     */
    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    /**
     * Установка профиля участника чата
     *
     * @param UserProfile $profile
     * @return AbstractUser
     */
    public function setProfile(UserProfile $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Получения ссылки на профиль участника чата в сторонней чат системе
     *
     * @return string|null
     */
    public function getProfileLink(): ?string
    {
        return $this->profileLink;
    }

    /**
     * Установка ссылки на профиль участника чата в сторонней чат системе
     *
     * @param string $profileLink
     * @return AbstractUser
     */
    public function setProfileLink(string $profileLink): self
    {
        $this->profileLink = $profileLink;
        return $this;
    }

    /**
     * Валидация на передаваемый ID с какой-либо стороны
     *
     * @return void
     */
    protected function validateUser(): void
    {
        if ($this->getId() === null && $this->getRefId() === null) {
            throw new AmoJoException('The ID or Ref ID parameter is mandatory for the chat participant.');
        }
    }

    /**
     * Возвращает массив Sender/Receiver для объекта Payload
     *
     * @return array
     */
    public function toPayload(): array
    {
        $this->validateUser();

        return array_filter([
            'id'           => $this->getId(),
            'ref_id'       => $this->getRefId(),
            'name'         => $this->getName(),
            'avatar'       => $this->getAvatar(),
            'profile_link' => $this->getProfileLink(),
            'profile'      => $this->getProfile() ? $this->getProfile()->toPayload() : null,
        ]);
    }
}
