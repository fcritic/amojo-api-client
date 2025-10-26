<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

interface ResponseInterface
{
    public static function fromArray(array $data): ResponseInterface;
    public function toArray(): array;
}
