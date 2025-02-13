<?php

declare(strict_types=1);

namespace AmoJo\Exception;

/**
 * Выбрасывается при не переданных обязательных параметрах в запрос
 *
 * @extends AmoJoException
 */
final class RequiredParametersMissingException extends AmoJoException
{
}
