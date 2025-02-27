<?php

declare(strict_types=1);

namespace AmoJo\Client;

interface ApiGatewayInterface
{
    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    public function get(string $uri, array $options): array;

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    public function post(string $uri, array $options): array;

    /**
     * @param string $uri
     * @param array $options
     * @return array
     */
    public function delete(string $uri, array $options): array;
}
