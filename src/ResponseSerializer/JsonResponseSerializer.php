<?php

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Serializer\JsonSerializer;

class JsonResponseSerializer extends AbstractResponseSerializer
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

    protected function serializeData($data): StreamInterface
    {
        return $this->createStream($this->jsonSerializer->serialize($data));
    }
}
