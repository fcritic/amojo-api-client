<?php

declare(strict_types=1);

namespace AmoJo\Client;

interface ApiGatewayInterface
{
    /**
     * @param string $uri
     * @param array $query
     * @return array
     */
    public function get(string $uri, array $query = []): array;

    /**
     * @param string $uri
     * @param array $data
     * @return array
     */
    public function post(string $uri, array $data = []): array;

    /**
     * @param string $uri
     * @param array $data
     * @return array
     */
    public function delete(string $uri, array $data = []): array;
}
