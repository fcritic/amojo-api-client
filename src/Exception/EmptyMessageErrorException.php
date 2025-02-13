<?php

declare(strict_types=1);

namespace AmoJo\Exception;

/**
 * Выбрасывается если при коде ошибки 905 доставки сообщения не было передано сообщение об ошибки
 *
 * @extends AmoJoException
 */
final class EmptyMessageErrorException extends AmoJoException
{
}
