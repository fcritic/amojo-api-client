<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\Sender;
use AmoJo\Models\Users\ValueObject\UserProfile;

final class CreateChatResponse implements ResponseInterface
{
    private string $conversationRefId;
    private UserInterface $user;

    public function __construct(string $conversationRefId, UserInterface $user)
    {
        $this->conversationRefId = $conversationRefId;
        $this->user = $user;
    }

    /**
     * Идентификатор чата в API чатов
     */
    public function getConversationRefId(): string
    {
        return $this->conversationRefId;
    }

    /**
     * Участник чата
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public static function fromArray(array $data): ResponseInterface
    {
        $user = isset($data['user']) && is_array($data['user'])
            ? $data['user']
            : [];

        return new self(
            (string)($data['id'] ?? ''),
            (new Sender())
                ->setRefId($user['id'] ?? '')
                ->setId($user['client_id'] ?? '')
                ->setName($user['name'] ?? '')
                ->setAvatar($user['avatar'] ?? '')
                ->setProfileLink($user['profile_link'] ?? '')
                ->setProfile((new UserProfile())
                    ->setPhone($user['phone'] ?? '')
                    ->setEmail($user['email'] ?? ''))
        );
    }

    public function toArray(): array
    {
        $userProfile = $this->user->getProfile();

        return [
            'id' => $this->conversationRefId,
            'user' => [
                'id' => $this->user->getRefId(),
                'client_id' => $this->user->getId(),
                'name' => $this->user->getName(),
                'avatar' => $this->user->getAvatar(),
                'phone' => $userProfile ? $userProfile->getPhone() : null,
                'email' => $userProfile ? $userProfile->getEmail() : null,
            ]
        ];
    }
}
