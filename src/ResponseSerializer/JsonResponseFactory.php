<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Serializer\JsonSerializer;

final class JsonResponseFactory extends AbstractResponseFactory
{
    private JsonSerializer $jsonSerializer;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        JsonSerializer $jsonSerializer,
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $factory
    ) {
        parent::__construct($factory);
        $this->jsonSerializer = $jsonSerializer;
        $this->streamFactory = $streamFactory;
    }

    protected function convertData($data): StreamInterface
    {
        $content = $this->jsonSerializer->serialize($data);

        return $this->streamFactory->createStream($content);
    }

    protected function getContentType(): string
    {
        return 'application/json';
    }
}
