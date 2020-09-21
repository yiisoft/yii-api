<?php

namespace Yiisoft\Yii\Api\Tests\Support\Controller;

use Psr\Http\Message\ResponseInterface;

final class GetController
{
    public function get(): ResponseInterface
    {
        throw new \Exception('Not implemented');
    }
}
