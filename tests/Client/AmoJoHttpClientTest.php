<?php

declare(strict_types=1);

namespace Tests\Client;

use AmoJo\Client\AmoJoHttpClient;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\InvalidResponseException;
use AmoJo\Exception\NotFountException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use ReflectionClass;
use ReflectionException;

class AmoJoHttpClientTest extends TestCase
{
    private function setMockClient(AmoJoHttpClient $httpClient, ClientInterface $client): void
    {
        $reflector = new ReflectionClass($httpClient);
        $property = $reflector->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($httpClient, $client);
    }

    public function testRequestSuccessfullyParsesResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['key' => 'value'], JSON_THROW_ON_ERROR))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        // Создаем объект как обычно
        $httpClient = new AmoJoHttpClient([], 'ru');

        // Устанавливаем мок-клиент через рефлексию
        $this->setMockClient($httpClient, $client);

        $result = $httpClient->get('/test', []);
        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testRequestHandlesClientExceptionWithEmptyBody(): void
    {
        $this->expectException(NotFountException::class);
        $this->expectExceptionMessage('Resource not found');

        $mock = new MockHandler([
            new ClientException(
                'Not Found',
                new Request('GET', 'test'),
                new Response(404)
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $httpClient = new AmoJoHttpClient([], 'ru');

        $this->setMockClient($httpClient, $client);
        $httpClient->get('/test');
    }

    public function testRequestHandlesClientExceptionWithErrorBody(): void
    {
        $this->expectException(AmoJoException::class);
        $this->expectExceptionMessage('Test error');

        $errorBody = json_encode([
            'error_description' => 'Test error',
            'error_code'        => 500,
            'error_type'        => 'TEST_ERROR'
        ], JSON_THROW_ON_ERROR);

        $mock = new MockHandler([
            new ClientException(
                'Error',
                new Request('GET', 'test'),
                new Response(500, [], $errorBody)
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $httpClient = new AmoJoHttpClient([], 'ru');

        $this->setMockClient($httpClient, $client);
        $httpClient->get('/test');
    }

    public function testParserResponseWithEmptyBody(): void
    {
        $httpClient = new AmoJoHttpClient([], 'ru');

        $response = new Response(204);
        $result = $this->invokePrivateMethod($httpClient, 'parserResponse', [$response]);

        $this->assertEquals([], $result);
    }

    public function testParserResponseThrowsOnInvalidJson(): void
    {
        $this->expectException(InvalidResponseException::class);

        $httpClient = new AmoJoHttpClient([], 'ru');

        $response = new Response(200, [], 'invalid-json');
        $this->invokePrivateMethod($httpClient, 'parserResponse', [$response]);
    }

    private function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        try {
            $reflector = new ReflectionClass($object);
            $method = $reflector->getMethod($methodName);
            $method->setAccessible(true);
            return $method->invokeArgs($object, $parameters);
        } catch (ReflectionException $e) {
            throw new AmoJoException($e->getMessage());
        }
    }
}
