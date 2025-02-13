<?php

declare(strict_types=1);

namespace AmoJo\DTO;

use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\Sender;
use AmoJo\Models\Users\ValueObject\UserProfile;

/**
 * DTO ответа на создания чата
 *
 * @extends AbstractResponse
 */
final class CreateChatResponse extends AbstractResponse
{
    /** @var string */
    private string $conversationRefId;

    /** @var UserInterface */
    private UserInterface $user;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->conversationRefId = $data['id'];
        $this->user = (new Sender())
            ->setRefId($data['user']['id'])
            ->setId($data['user']['client_id'])
            ->setName($data['user']['name'] ?? '')
            ->setAvatar($data['user']['avatar'] ?? '')
            ->setProfileLink($data['user']['profile_link'] ?? '')
            ->setProfile(
                (new UserProfile())
                    ->setPhone($data['user']['phone'] ?? '')
                    ->setEmail($data['user']['email'] ?? '')
            );
    }

    /**
     * Идентификатор чата в API чатов
     *
     * @return string
     */
    public function getConversationRefId(): string
    {
        return $this->conversationRefId;
    }

    /**
     * Участник чата
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
