<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\ResponseFactory;

use Psr\Http\Message\ResponseFactoryInterface as PsrResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Http\Header;
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

        return $this->responseFactory
            ->createResponse()
            ->withHeader(Header::CONTENT_TYPE, $this->getContentType())
            ->withBody($stream);
    }

    abstract protected function convertData($data): StreamInterface;

    abstract protected function getContentType(): string;
}
