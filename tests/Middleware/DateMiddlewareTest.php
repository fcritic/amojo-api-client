<?php

declare(strict_types=1);

namespace Tests\Middleware;

use AmoJo\Enum\HeaderType;
use AmoJo\Middleware\DateMiddleware;
use DateTimeInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class DateMiddlewareTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddsDateHeader(): void
    {
        $middleware = new DateMiddleware();

        $handler = function ($request) {
            $request->getHeaderLine(HeaderType::DATE);
            $dateHeader = $request->getHeaderLine(HeaderType::DATE);

            // Проверяем формат RFC 2822
            $this->assertRegExp(
                '/^\w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}$/',
                $dateHeader
            );

            // Проверяем что дата примерно текущая
            $parsedDate = \DateTime::createFromFormat(DateTimeInterface::RFC2822, $dateHeader);
            $this->assertLessThanOrEqual(5, abs(time() - $parsedDate->getTimestamp()));

            return new Response();
        };

        $middleware->__invoke($handler)(new Request('GET', '/'), []);
    }
}
