<?php

declare(strict_types=1);

namespace Tests\Middleware;

use AmoJo\Enum\HeaderType;
use AmoJo\Middleware\ContentMD5Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @extends TestCase
 */
class ContactMD5MiddlewareTest extends TestCase
{
    /**
     * Проверяем расчет MD5 для разных типов запросов
     * @dataProvider requestProvider
     * @param string $method
     * @param string $body
     * @param string $expectedHash
     * @return void
     */
    public function testAddsContentMd5Header(string $method, string $body, string $expectedHash): void
    {
        $middleware = new ContentMD5Middleware();
        $handler = function (RequestInterface $request) use ($expectedHash) {
            $this->assertEquals(
                $expectedHash,
                $request->getHeaderLine(HeaderType::CONTENT_MD5)
            );
            return new Response();
        };

        $request = new Request($method, '/', [], $body);

        $middleware->__invoke($handler)($request, []);
    }

    /**
     * @return array[]
     */
    public function requestProvider(): array
    {
        return [
            'GET empty body' => ['GET', '', 'd41d8cd98f00b204e9800998ecf8427e'],
            'POST with body' => ['POST', 'test', '098f6bcd4621d373cade4e832627b4f6']
        ];
    }
}
