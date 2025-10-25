<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\ValueObject\UserProfile;

trait UserBuilderTrait
{
    /**
     * @param array $data
     * @param string $class
     * @return SenderInterface|ReceiverInterface
     */
    protected function buildUser(array $data, string $class): UserInterface
    {
        $user = (new $class())
            ->setRefId($data['id'] ?? '')
            ->setName($data['name'] ?? '');

        if (isset($data['client_id'])) {
            $user->setId($data['client_id']);
        }

        if ($user instanceof ReceiverInterface) {
            $user->setProfile(
                (new UserProfile())
                    ->setPhone($data['phone'] ?? '')
                    ->setEmail($data['email'] ?? '')
            );
        }

        return $user;
    }
}
