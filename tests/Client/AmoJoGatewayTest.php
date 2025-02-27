<?php

declare(strict_types=1);

namespace Tests\Client;

use AmoJo\Client\AmoJoGateway;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\InvalidResponseException;
use AmoJo\Exception\NotFountException;
use AmoJo\Models\Channel;
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

/**
 * @extends TestCase
 */
class AmoJoGatewayTest extends TestCase
{
    /**
     * @param AmoJoGateway $gateway
     * @param ClientInterface $client
     * @return void
     */
    private function setMockClient(AmoJoGateway $gateway, ClientInterface $client): void
    {
        $reflector = new ReflectionClass($gateway);
        $property = $reflector->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($gateway, $client);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testRequestSuccessfullyParsesResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['key' => 'value'], JSON_THROW_ON_ERROR))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        // Создаем объект как обычно
        $gateway = new AmoJoGateway([], 'ru');

        // Устанавливаем мок-клиент через рефлексию
        $this->setMockClient($gateway, $client);

        $result = $gateway->get('/test', []);
        $this->assertEquals(['key' => 'value'], $result);
    }

    /**
     * @return void
     */
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

        $gateway = new AmoJoGateway([], 'ru');

        $this->setMockClient($gateway, $client);
        $gateway->get('/test');
    }

    /**
     * @return void
     * @throws JsonException
     */
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

        $gateway = new AmoJoGateway([], 'ru');

        $this->setMockClient($gateway, $client);
        $gateway->get('/test');
    }

    /**
     * @return void
     */
    public function testParserResponseWithEmptyBody(): void
    {
        $gateway = new AmoJoGateway([], 'ru');

        $response = new Response(204);
        $result = $this->invokePrivateMethod($gateway, 'parserResponse', [$response]);

        $this->assertEquals(['status' => 204], $result);
    }

    /**
     * @return void
     */
    public function testParserResponseThrowsOnInvalidJson(): void
    {
        $this->expectException(InvalidResponseException::class);

        $gateway = new AmoJoGateway([], 'ru');

        $response = new Response(200, [], 'invalid-json');
        $this->invokePrivateMethod($gateway, 'parserResponse', [$response]);
    }

    /**
     * @param $object
     * @param string $methodName
     * @param array $parameters
     * @return object|mixed
     */
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
