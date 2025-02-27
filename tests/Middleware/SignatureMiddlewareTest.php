<?php

declare(strict_types=1);

namespace Tests\Middleware;

use AmoJo\Enum\HeaderType;
use AmoJo\Exception\RequiredParametersMissingException;
use AmoJo\Middleware\SignatureMiddleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class SignatureMiddlewareTest extends TestCase
{
    private const SECRET_KEY = 'test_secret_123';
    private const TEST_PATH = '/v2/origin/custom/scope123';

    /**
     * @dataProvider signatureDataProvider
     */
    public function testAddsSignatureHeader(
        string $method,
        array $headers,
        string $path,
        string $expectedSignature
    ): void {
        $middleware = new SignatureMiddleware();
        $request = new Request($method, $path, $headers, '{}');

        $handler = function ($request) use ($expectedSignature) {
            $this->assertEquals(
                $expectedSignature,
                $request->getHeaderLine(HeaderType::SIGNATURE)
            );
            return new Response();
        };

        $middleware->__invoke($handler)(
            $request,
            ['secret_key' => self::SECRET_KEY]
        );
    }

    public function testThrowsExceptionWithoutSecretKey(): void
    {
        $this->expectException(RequiredParametersMissingException::class);
        $this->expectExceptionMessage('Secret key is required.');

        $middleware = new SignatureMiddleware();
        $request = new Request('POST', self::TEST_PATH);

        $handler = function ($request) {
            return new Response();
        };

        $middleware->__invoke($handler)($request, []);
    }

    public function signatureDataProvider(): array
    {
        $baseDate = 'Wed, 21 Oct 2023 07:28:00 GMT';
        $baseHeaders = [
            HeaderType::CONTENT_MD5 => 'd41d8cd98f00b204e9800998ecf8427e',
            HeaderType::CONTENT_TYPE => 'application/json',
            HeaderType::DATE => $baseDate,
        ];

        return [
            'POST request with all headers' => [
                'method' => 'POST',
                'headers' => $baseHeaders,
                'path' => self::TEST_PATH,
                'expectedSignature' => $this->calculateSignature(
                    'POST',
                    $baseHeaders
                )
            ],
            'GET request with missing headers' => [
                'method' => 'GET',
                'headers' => [HeaderType::DATE => $baseDate],
                'path' => self::TEST_PATH,
                'expectedSignature' => $this->calculateSignature(
                    'GET',
                    [HeaderType::DATE => $baseDate]
                )
            ],
            'PUT request with empty values' => [
                'method' => 'PUT',
                'headers' => [
                    HeaderType::CONTENT_MD5 => '',
                    HeaderType::CONTENT_TYPE => '',
                    HeaderType::DATE => ''
                ],
                'path' => self::TEST_PATH,
                'expectedSignature' => $this->calculateSignature(
                    'PUT',
                    [
                        HeaderType::CONTENT_MD5 => '',
                        HeaderType::CONTENT_TYPE => '',
                        HeaderType::DATE => ''
                    ]
                )
            ]
        ];
    }

    private function calculateSignature(
        string $method,
        array $headers
    ): string {
        $headers = array_merge(
            [
                HeaderType::CONTENT_MD5 => '',
                HeaderType::CONTENT_TYPE => '',
                HeaderType::DATE => ''
            ],
            $headers
        );

        $str = implode("\n", [
            strtoupper($method),
            $headers[HeaderType::CONTENT_MD5],
            $headers[HeaderType::CONTENT_TYPE],
            $headers[HeaderType::DATE],
            self::TEST_PATH
        ]);

        return strtolower(hash_hmac('sha1', $str, self::SECRET_KEY));
    }
}
