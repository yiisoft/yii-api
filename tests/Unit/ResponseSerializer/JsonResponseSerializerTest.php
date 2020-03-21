<?php

namespace Yiisoft\Yii\Rest\Tests\Unit\ResponseSerializer;

use Nyholm\Psr7\Factory\Psr17Factory;
use Yiisoft\Serializer\JsonSerializer;
use Yiisoft\Yii\Rest\ResponseSerializer\JsonResponseSerializer;
use Yiisoft\Yii\Rest\ResponseSerializerInterface;

class JsonResponseSerializerTest extends AbstractResponseSerializerTestCase
{
    protected function getSerializer(): ResponseSerializerInterface
    {
        $jsonSerializer = new JsonSerializer();
        $factory = new Psr17Factory();

        return new JsonResponseSerializer($jsonSerializer, $factory, $factory);
    }
}
