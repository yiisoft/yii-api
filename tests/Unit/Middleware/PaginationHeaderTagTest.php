<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\Tests\Unit\Middleware;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Yii\Rest\Middleware\PaginationHeaderTag;

/**
 * @covers \Yiisoft\Yii\Rest\Middleware\PaginationHeaderTag
 */
final class PaginationHeaderTagTest extends TestCase
{
    public function testProcess(): void
    {
        $response = new Response();
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMirrorHandler($response);

        $middleware = new PaginationHeaderTag();
        $processedResponse = $middleware->process($request, $handler);

        $this->assertTrue($processedResponse->hasHeader('Link'));
        $this->assertTrue($processedResponse->hasHeader('X-Pagination-Total-Count'));
        $this->assertTrue($processedResponse->hasHeader('X-Pagination-Page-Count'));
        $this->assertTrue($processedResponse->hasHeader('X-Pagination-Current-Page'));
        $this->assertTrue($processedResponse->hasHeader('X-Pagination-Per-Page'));
    }

    private function createMirrorHandler(ResponseInterface $response): RequestHandlerInterface
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        return $handler;
    }
}
