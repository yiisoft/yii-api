<?php

namespace Yiisoft\Yii\Rest;

use Psr\Container\ContainerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Yii\Rest\Middleware\ResponseConverter;

class RestGroup
{
    public static function create(
        string $prefix,
        string $controller,
        ContainerInterface $container
    ): RouteCollectorInterface {
        $routes = self::createDefaultRoutes($controller, $container);
        $group = Group::create($prefix, $routes);

        // add middlewares here

        return $group;
    }

    private static function createDefaultRoutes(string $controller, ContainerInterface $container): array
    {
        $reflection = new \ReflectionClass($controller);
        $methods = [
            'get' => Method::GET,
            'list' => Method::GET,
            'post' => Method::POST,
            'put' => Method::PUT,
            'delete' => Method::DELETE,
            'patch' => Method::PATCH,
            'head' => Method::HEAD,
            'options' => Method::OPTIONS,
        ];
        $routes = [];

        foreach ($methods as $methodName => $httpMethod) {
            if ($reflection->hasMethod($methodName)) {
                $pattern = $methodName === 'list' ? '' : '/{id:[^/]+}';
                $middleware = new ResponseConverter($controller, $methodName, $container);
                $routes[] = Route::methods([$httpMethod], $pattern, $middleware);
            }
        }

        return $routes;
    }
}
