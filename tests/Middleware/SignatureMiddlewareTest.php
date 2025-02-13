<?php

declare(strict_types=1);

namespace Tests\Middleware;

use AmoJo\Enum\HeaderType;
use AmoJo\Middleware\SignatureMiddleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class SignatureMiddlewareTest extends TestCase
{
    /** @var string */
    private const SECRET = 'test-secret-key-123';

    /**
     * @return void
     */
    public function testAddsSignatureHeader(): void
    {
        $middleware = new SignatureMiddleware(self::SECRET);

        $request = new Request(
            'POST',
            '/v2/origin/custom/{scope_id}',
            [
                HeaderType::CONTENT_MD5  => 'd41d8cd98f00b204e9800998ecf8427e',
                HeaderType::CONTENT_TYPE => 'application/json',
                HeaderType::DATE         => 'Wed, 21 Oct 2023 07:28:00 GMT'
            ],
            '{}'
        );

        $handler = function ($request) {
            $expectedSignature = strtolower(hash_hmac(
                'sha1',
                implode("\n", [
                    strtoupper($request->getMethod()),
                    $request->getHeaderLine(HeaderType::CONTENT_MD5),
                    $request->getHeaderLine(HeaderType::CONTENT_TYPE),
                    $request->getHeaderLine(HeaderType::DATE),
                    $request->getUri()->getPath()
                ]),
                self::SECRET
            ));

            $this->assertEquals(
                $expectedSignature,
                $request->getHeaderLine(HeaderType::SIGNATURE)
            );

            return new Response();
        };

        $middleware->__invoke($handler)($request, []);
    }
}
