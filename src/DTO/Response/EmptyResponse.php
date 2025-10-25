<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

final class EmptyResponse implements ResponseInterface
{
    public static function fromArray(array $data): ResponseInterface
    {
        return new self();
    }

    public function toArray(): array
    {
        return [];
    }
}
