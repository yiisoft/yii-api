<?php

namespace Yiisoft\Yii\Rest;

use Psr\Container\ContainerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Yii\Rest\Middleware\ResponseConverter;

class RestGroup
{
    public static function create(string $prefix, string $controller, ContainerInterface $container): Group
    {
        $routes = self::createDefaultRoutes($controller, $container);

        return Group::create($prefix, $routes);
    }

    private static function createDefaultRoutes(string $controller, ContainerInterface $container): array
    {
        $methods = [
            'list' => Method::GET,
            'get' => Method::GET,
            'post' => Method::POST,
            'put' => Method::PUT,
            'delete' => Method::DELETE,
            'patch' => Method::PATCH,
            'head' => Method::HEAD,
            'options' => Method::OPTIONS,
        ];
        $routes = [];

        $controllerActions = get_class_methods($controller);
        foreach ($methods as $methodName => $httpMethod) {
            if (in_array($methodName, $controllerActions, true)) {
                $pattern = $methodName === 'list' ? '' : '/{id:[^/]+}';
                $middleware = new ResponseConverter($controller, $methodName, $container);
                $routes[] = Route::methods([$httpMethod], $pattern, $middleware);
            }
        }

        return $routes;
    }
}
