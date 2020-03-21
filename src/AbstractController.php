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
    private ResponseSerializerInterface $responseSerializer;

    public function __construct(ResponseSerializerInterface $responseSerializer)
    {
        $this->responseSerializer = $responseSerializer;
    }

    protected function render(int $code, ...$data): ResponseInterface
    {
        return $this->responseSerializer->serialize($code, $data);
    }
}
