<?php

declare(strict_types=1);

namespace AmoJo\Client;

use AmoJo\Enum\HeaderType;
use AmoJo\Enum\HttpMethod;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\InvalidResponseException;
use AmoJo\Exception\NotFountException;
use AmoJo\Middleware\StackMiddleware;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Объект для выполнения http запросов
 */
class AmoJoHttpClient
{
    private const BASE_PATH = '/v2/origin/custom/';
    private const CONNECTION_TIMEOUT = 20;
    private const HOST = [
        'ru'  => 'https://amojo.amocrm.ru',
        'com' => 'https://amojo.kommo.com',
    ];

    protected ClientInterface $client;

    public function __construct(array $additionalMiddleware, string $segment)
    {
        $stack = $this->registerMiddleware($additionalMiddleware);

        $this->client = new HttpClient([
            'base_uri' => self::HOST[$segment],
            'timeout' => self::CONNECTION_TIMEOUT,
            'handler' => $stack,
            'headers' => [
                HeaderType::CONTENT_TYPE => 'application/json',
                HeaderType::USER_AGENT => 'amoJo-PHP-Client/1.3.1',
            ]
        ]);
    }

    private function registerMiddleware(array $middlewares): HandlerStack
    {
        $stack = HandlerStack::create();
        foreach (StackMiddleware::create($middlewares) as $middleware) {
            $stack->push($middleware);
        }

        return $stack;
    }

    public function get(string $uri, array $options = []): array
    {
        return $this->executeRequest(HttpMethod::GET_REQUEST, $uri, $options);
    }

    public function post(string $uri, array $options): array
    {
        return $this->executeRequest(HttpMethod::POST_REQUEST, $uri, $options);
    }

    public function delete(string $uri, array $options): array
    {
        return $this->executeRequest(HttpMethod::DELETE_REQUEST, $uri, $options);
    }

    private function executeRequest(string $method, string $uri, array $options): array
    {
        try {
            $response = $this->client->request($method, sprintf('%s%s', self::BASE_PATH, $uri), $options);

            return $this->parserResponse($response);
        } catch (ClientException $e) {
            $this->handleClientError($e);
        } catch (GuzzleException $e) {
            throw new AmoJoException(sprintf('Request failed: %s', $e->getMessage()), 0, 'UNKNOWN_ERROR');
        }
    }

    private function parserResponse(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        if (empty($body)) {
            return [];
        }

        try {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidResponseException(sprintf('Invalid JSON: %s', $e->getMessage()), 0, 'UNKNOWN_ERROR');
        }
    }

    private function handleClientError(ClientException $e): void
    {
        try {
            $response = $e->getResponse();
            $body = (string)$response->getBody();

            if (empty($body)) {
                throw new NotFountException('Resource not found', 404, 'INVALID_URI');
            }

            $errorData = json_decode($body, true, 512, JSON_THROW_ON_ERROR) ?? [];
            $context = [
                'uri' => $e->getRequest()->getUri(),
                'method' => $e->getRequest()->getMethod() ?? '',
                'status' => $e->getResponse()->getStatusCode() ?? 0,
                'body' => (string)$e->getRequest()->getBody(),
            ];

            throw new AmoJoException(
                $errorData['error_description'] ?? 'Unknown error',
                $errorData['error_code'] ?? $response->getStatusCode(),
                $errorData['error_type'] ?? 'UNKNOWN_ERROR',
                $context
            );
        } catch (JsonException $e) {
            throw new InvalidResponseException(sprintf('Failed decoding: %s', $e->getMessage()));
        }
    }
}
