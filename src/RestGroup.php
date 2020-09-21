<?php

namespace Yiisoft\Yii\Api;

use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;

class RestGroup
{
    public static function create(
        string $prefix,
        string $controller
    ): RouteCollectorInterface {
        $routes = self::createDefaultRoutes($controller);

        return Group::create($prefix, $routes);
    }

    private static function createDefaultRoutes(string $controller): array
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
                $routes[] = Route::methods([$httpMethod], $pattern, [$controller, $methodName]);
            }
        }

        return $routes;
    }
}
