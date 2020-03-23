<?php

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Serializer\JsonSerializer;

final class JsonResponseFactory extends AbstractResponseFactory
{
    private JsonSerializer $jsonSerializer;

    public function __construct(
        JsonSerializer $jsonSerializer,
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $factory
    ) {
        parent::__construct($streamFactory, $factory);
        $this->jsonSerializer = $jsonSerializer;
    }

    protected function convertData($data): StreamInterface
    {
        return $this->createStream($this->jsonSerializer->serialize($data));
    }
}
