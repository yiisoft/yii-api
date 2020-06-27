<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\Tests\Unit\ResponseFactory;

use Nyholm\Psr7\Factory\Psr17Factory;
use Yiisoft\Serializer\JsonSerializer;
use Yiisoft\Yii\Rest\ResponseFactory\JsonResponseFactory;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;

final class JsonResponseFactoryTest extends AbstractResponseFactoryTestCase
{
    protected function getFactory(): ResponseFactoryInterface
    {
        $jsonSerializer = new JsonSerializer();
        $factory = new Psr17Factory();

        return new JsonResponseFactory($jsonSerializer, $factory, $factory);
    }

    public function testContentTypeInResponse(): void
    {
        $factory = $this->getFactory();
        $response = $factory->createResponse([]);

        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }
}
