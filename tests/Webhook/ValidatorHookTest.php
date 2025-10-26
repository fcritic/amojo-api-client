<?php

declare(strict_types=1);

namespace Tests\Webhook;

use AmoJo\Exception\InvalidRequestWebHookException;
use AmoJo\Webhook\ValidatorHook;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class ValidatorHookTest extends TestCase
{
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

    public function testValidSignature(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = 'test-body';
        $validSignature = hash_hmac('sha1', $body, $secret);

        $request = $this->createRequestMock($body, $validSignature);

        $result = ValidatorHook::isValid($request, $secret);

        $this->assertTrue($result);
    }

    public function testInvalidSignature(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = 'test-body';
        $invalidSignature = 'wrong-signature';

        $request = $this->createRequestMock($body, $invalidSignature);

        $result = ValidatorHook::isValid($request, $secret);

        $this->assertFalse($result);
    }

    public function testMissingSignatureHeader(): void
    {
        $request = $this->createRequestMock('body-content', null);

        $result = ValidatorHook::isValid($request, '11c08dd7ba836ea9cfc03133b4813d');

        $this->assertFalse($result);
    }

    public function testTrimBodyContent(): void
    {
        $secret = '11c08dd7ba836ea9cfc03133b4813d';
        $body = "  \ntest-body\n  ";
        $expectedSignature = hash_hmac('sha1', trim($body, "\n"), $secret);

        $request = $this->createRequestMock($body, $expectedSignature);

        $result = ValidatorHook::isValid($request, $secret);

        $this->assertTrue($result);
    }

    public function testExceptionOnInvalidBodyProcessing(): void
    {
        $this->expectException(InvalidRequestWebHookException::class);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willThrowException(new \RuntimeException());

        ValidatorHook::isValid($request, '11c08dd7ba836ea9cfc03133b4813d');
    }

    public function testEmptySecretKey(): void
    {
        $body = 'test-body';
        $signature = hash_hmac('sha1', $body, '');

        $request = $this->createRequestMock($body, $signature);

        $result = ValidatorHook::isValid($request, '');

        $this->assertTrue($result);
    }
}
