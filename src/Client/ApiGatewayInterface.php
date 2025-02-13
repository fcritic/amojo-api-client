<?php

declare(strict_types=1);

namespace AmoJo\Client;

interface ApiGatewayInterface
{
    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return array
     */
    public function request(string $method, string $uri, array $data = [], array $query = []): array;
}
