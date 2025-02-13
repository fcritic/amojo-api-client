<?php

declare(strict_types=1);

namespace AmoJo\Models\Users;

use AmoJo\Models\Interfaces\ReceiverInterface;

/**
 * Отправитель сообщения. Обязательный объект для исходящего сообщения
 *
 * При исходящим сообщение принимает параметры контакта. Никогда не принимает параметры пользователя amoCRM
 * @implements ReceiverInterface
 * @extends AbstractUser
 */
final class Receiver extends AbstractUser implements ReceiverInterface
{
}
