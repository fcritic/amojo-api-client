<?php

declare(strict_types=1);

namespace App\Services\Webhook\Trait;

use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Users\ValueObject\UserProfile;

trait UserParserTrait
{

    /**
     * @param array $data
     * @param string $class
     * @return UserInterface
     */
    protected function parseUser(array $data, string $class): UserInterface
    {
        $user = (new $class())
            ->setRefId($data['id'])
            ->setName($data['name'] ?? '');

        if (isset($data['client_id'])) {
            $user->setId($data['client_id']);
        }

        if (isset($data['phone']) || isset($data['email'])) {
            $user->setProfile((new UserProfile()))
                    ->setPhone($data['phone'] ?? '')
                    ->setEmail($data['email'] ?? '');
        }

        return $user;
    }
}
