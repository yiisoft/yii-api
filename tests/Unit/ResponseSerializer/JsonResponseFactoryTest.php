<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\Tests\Unit\ResponseSerializer;

use Nyholm\Psr7\Factory\Psr17Factory;
use Yiisoft\Serializer\JsonSerializer;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;
use Yiisoft\Yii\Rest\ResponseSerializer\JsonResponseFactory;

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
        $serializer = $this->getFactory();
        $response = $serializer->createResponse([]);

        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }
}
