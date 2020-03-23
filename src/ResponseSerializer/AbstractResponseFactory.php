<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\ResponseSerializer;

use Psr\Http\Message\ResponseFactoryInterface as PsrResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;

abstract class AbstractResponseFactory implements ResponseFactoryInterface
{
    private PsrResponseFactoryInterface $responseFactory;

    public function __construct(PsrResponseFactoryInterface $factory)
    {
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
}
