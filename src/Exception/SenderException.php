<?php

declare(strict_types=1);

namespace AmoJo\Exception;

/**
 * Выбрасывается если при передачах объекта ReceiverInterface не был передан SenderInterface->ref_id
 *
 * @extends AmoJoException
 */
final class SenderException extends AmoJoException
{
}
