<?php

declare(strict_types=1);

namespace AmoJo\Client;

use AmoJo\Enum\HeaderType;
use AmoJo\Enum\HttpMethod;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\InvalidResponseException;
use AmoJo\Exception\NotFountException;
use AmoJo\Middleware\StackMiddleware;
use AmoJo\Middleware\MiddlewareInterface;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Объект для выполнения http запросов
 *
 * @implements ApiGatewayInterface
 */
class AmoJoGateway implements ApiGatewayInterface
{
    /** @var string */
    private const BASE_PATH = '/v2/origin/custom/';

    /** @var array|string[] Задается при создании объекта AmoJoApiClient */
    private const HOST = [
        'ru'  => 'https://amojo.amocrm.ru',
        'com' => 'https://amojo.kommo.com',
    ];

    /** @var int */
    private const CONNECTION_TIMEOUT = 20;

    /** @var ClientInterface PSR-18 HTTP-клиент для выполнения запросов.*/
    protected ClientInterface $client;

    /**
     * @param array $additionalMiddleware массив с кастомными middleware implements \Middleware\MiddlewareInterface
     * @param string $segment ru | com
     */
    public function __construct(array $additionalMiddleware, string $segment)
    {
        $stack = $this->registerMiddleware($additionalMiddleware);

        $this->client = new HttpClient([
            'base_uri' => self::HOST[$segment],
            'timeout'  => self::CONNECTION_TIMEOUT,
            'handler'  => $stack,
            'headers'  => [
                HeaderType::CONTENT_TYPE => 'application/json',
                HeaderType::USER_AGENT   => 'amoJo-PHP-Client/1.2.1',
            ]
        ]);
    }

    /**
     * Регистрация Middleware в клиента
     *
     * @param array<class-string<MiddlewareInterface>> $middlewares
     * @return HandlerStack
     */
    private function registerMiddleware(array $middlewares): HandlerStack
    {
        $stack = HandlerStack::create();
        foreach (StackMiddleware::create($middlewares) as $middleware) {
            $stack->push($middleware);
        }

        return $stack;
    }

    /**
     * @param string $uri
     * @param array $query
     * @return array
     */
    public function get(string $uri, array $options = []): array
    {
        return $this->executeRequest(HttpMethod::GET_REQUEST, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $data
     * @return array
     */
    public function post(string $uri, array $options): array
    {
        return $this->executeRequest(HttpMethod::POST_REQUEST, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $data
     * @return array
     */
    public function delete(string $uri, array $options): array
    {
        return $this->executeRequest(HttpMethod::DELETE_REQUEST, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     */
    private function executeRequest(string $method, string $uri, array $options): array
    {
        try {
            $response = $this->client->request($method, self::BASE_PATH . $uri, $options);

            return $this->parserResponse($response);
        } catch (ClientException $e) {
            $this->handleClientError($e);
        } catch (GuzzleException $e) {
            throw new AmoJoException('Request failed: ' . $e->getMessage(), 0, 'UNKNOWN_ERROR');
        }
    }

    /**
     * Метод парсит Json в массив для дальнейшей передачи в метод клиента
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function parserResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if (empty($body)) {
            return [
                'status' => $response->getStatusCode(),
            ];
        }

        try {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidResponseException('Invalid JSON: ' . $e->getMessage(), 0, 'UNKNOWN_ERROR');
        }
    }

    /**
     * Обрабатывает ошибки API
     *
     * @param ClientException $e
     * @return void
     */
    private function handleClientError(Exception $e): void
    {
        try {
            $response = $e->getResponse();
            $body = (string) $response->getBody();

            if (empty($body)) {
                throw new NotFountException('Resource not found', 404, 'INVALID_URI');
            }

            $errorData = json_decode($body, true, 512, JSON_THROW_ON_ERROR) ?? [];
            $context = [
                'uri'    => $e->getRequest()->getUri(),
                'method' => $e->getRequest()->getMethod() ?? '',
                'status' => $e->getResponse()->getStatusCode() ?? 0,
                'body'   => (string) $e->getRequest()->getBody(),
            ];

            throw new AmoJoException(
                $errorData['error_description'] ?? 'Unknown error',
                $errorData['error_code'] ?? $response->getStatusCode(),
                $errorData['error_type'] ?? 'UNKNOWN_ERROR',
                $context
            );
        } catch (JsonException $e) {
            throw new InvalidResponseException('Failed decoding: ' . $e->getMessage());
        }
    }
}
