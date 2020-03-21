<?php

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Yii\Rest\ResponseSerializerInterface;

abstract class AbstractResponseSerializer implements ResponseSerializerInterface
{
    protected StreamFactoryInterface $streamFactory;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(StreamFactoryInterface $streamFactory, ResponseFactoryInterface $factory)
    {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $factory;
    }

    public function serialize(int $code, $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($code);

        return $response->withBody($this->serializeData($data));
    }

    abstract protected function serializeData($data): StreamInterface;

    protected function createStream(string $content): StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }
}
