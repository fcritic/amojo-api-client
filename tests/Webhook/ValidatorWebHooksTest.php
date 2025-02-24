<?php

declare(strict_types=1);

namespace Tests\Helpers;

use AmoJo\Exception\InvalidRequestWebHookException;
use AmoJo\Webhook\ValidatorWebHooks;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @extends TestCase
 */
class ValidatorWebHooksTest extends TestCase
{
    /**
     * @param string $bodyContent
     * @param string|null $signature
     * @return RequestInterface
     */
    private function createRequestMock(string $bodyContent, string $signature = null): RequestInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($bodyContent);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getHeaderLine')
            ->with('X-Signature')
            ->willReturn($signature ?? '');

        return $request;
    }

    /**
     * @return void
     */
    public function testValidSignature(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = 'test-body';
        $validSignature = hash_hmac('sha1', $body, $secret);

        $request = $this->createRequestMock($body, $validSignature);

        $result = ValidatorWebHooks::isValid($request, $secret);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testInvalidSignature(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = 'test-body';
        $invalidSignature = 'wrong-signature';

        $request = $this->createRequestMock($body, $invalidSignature);

        $result = ValidatorWebHooks::isValid($request, $secret);

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testMissingSignatureHeader(): void
    {
        $request = $this->createRequestMock('body-content', null);

        $result = ValidatorWebHooks::isValid($request, '11c08dd7ba836ea9cfc03133b4813d');

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testTrimBodyContent(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = "  \ntest-body\n  ";
        $expectedSignature = hash_hmac('sha1', trim($body, "\n"), $secret);

        $request = $this->createRequestMock($body, $expectedSignature);

        $result = ValidatorWebHooks::isValid($request, $secret);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testExceptionOnInvalidBodyProcessing(): void
    {
        $this->expectException(InvalidRequestWebHookException::class);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willThrowException(new \RuntimeException());

        ValidatorWebHooks::isValid($request, '11c08dd7ba836ea9cfc03133b4813d');
    }

    /**
     * @return void
     */
    public function testEmptySecretKey(): void
    {
        $body = 'test-body';
        $signature = hash_hmac('sha1', $body, '');

        $request = $this->createRequestMock($body, $signature);

        $result = ValidatorWebHooks::isValid($request, '');

        $this->assertTrue($result);
    }
}
