<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;

final class ActionCaller
{
    private string $class;
    private string $method;
    private ContainerInterface $container;

    public function __construct(string $class, string $action, ContainerInterface $container)
    {
        $this->method = $action;
        $this->class = $class;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $controller = $this->container->get($this->class);
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);
        $mixedResponse = (new Injector($this->container))->invoke([$controller, $this->method], [$request, $handler]);
        if ($mixedResponse instanceof ResponseInterface) {
            return $mixedResponse;
        }

        return $responseFactory->createResponse($mixedResponse);
    }
}
