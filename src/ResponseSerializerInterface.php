<?php

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

interface ResponseSerializerInterface
{
    public function serialize(int $code, $data): ResponseInterface;
}
