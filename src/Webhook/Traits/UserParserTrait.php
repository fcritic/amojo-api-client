<?php

declare(strict_types=1);

namespace AmoJo\Webhook\Traits;

use AmoJo\Models\Interfaces\ReceiverInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\ValueObject\UserProfile;

trait UserParserTrait
{
    /**
     * Создания как получателя, так и отправителя
     *
     * @param array $data
     * @param string $class
     * @return UserInterface
     */
    protected function parseUser(array $data, string $class): UserInterface
    {
        $user = (new $class())
            ->setRefId($data['id'])
            ->setName($data['name'] ?? '');

        if ($user instanceof ReceiverInterface) {
            if (isset($data['client_id'])) {
                $user->setId($data['client_id']);
            }

            $user->setProfile(
                (new UserProfile())
                    ->setPhone($data['phone'] ?? '')
                    ->setEmail($data['email'] ?? '')
            );
        }

        return $user;
    }
}
