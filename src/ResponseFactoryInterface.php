<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function createResponse($data): ResponseInterface;
}
