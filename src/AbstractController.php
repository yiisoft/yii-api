<?php

namespace Yiisoft\Yii\Rest;

use Psr\Http\Message\ResponseInterface;

/**
 * ActiveController implements a common set of actions for supporting RESTful.
 * For more details and usage information on ActiveController,
 * see the [guide article on rest controllers](guide:rest-controllers).
 */
abstract class AbstractController
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    protected function createResponse($data): ResponseInterface
    {
        return $this->responseFactory->createResponse($data);
    }
}
