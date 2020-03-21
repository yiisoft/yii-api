<?php

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

interface ResponseSerializerInterface
{
    // TODO may be $code is redundant?
    public function serialize(int $code, $data): ResponseInterface;
}
