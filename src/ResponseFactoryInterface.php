<?php

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function createResponse($data): ResponseInterface;
}
