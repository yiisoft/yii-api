<?php

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

interface ResponseSerializerInterface
{
    public function serialize($data): ResponseInterface;
}
