<?php

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface as PsrResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;

abstract class AbstractResponseFactory implements ResponseFactoryInterface
{
    protected StreamFactoryInterface $streamFactory;
    private PsrResponseFactoryInterface $responseFactory;

    public function __construct(StreamFactoryInterface $streamFactory, PsrResponseFactoryInterface $factory)
    {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $factory;
    }

    public function createResponse($data): ResponseInterface
    {
        $stream = $this->convertData($data);
        $stream->rewind();

        return $this
            ->responseFactory->createResponse()
            ->withHeader('Content-Type', $this->getContentType())
            ->withBody($stream);
    }

    abstract protected function convertData($data): StreamInterface;

    abstract protected function getContentType(): string;

    protected function createStream(string $content): StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }
}
