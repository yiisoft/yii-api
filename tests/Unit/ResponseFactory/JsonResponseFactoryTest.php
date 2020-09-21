<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Api\Tests\Unit\ResponseFactory;

use Nyholm\Psr7\Factory\Psr17Factory;
use Yiisoft\Serializer\JsonSerializer;
use Yiisoft\Yii\Api\ResponseFactory\JsonResponseFactory;
use Yiisoft\Yii\Api\ResponseFactoryInterface;

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

        $this->assertTrue($response->hasHeader(\Yiisoft\Http\Header::CONTENT_TYPE));
        $this->assertEquals('application/json', $response->getHeaderLine(\Yiisoft\Http\Header::CONTENT_TYPE));
    }
}
