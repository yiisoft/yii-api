<?php

namespace Yiisoft\Yii\Rest;

use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;

class RestGroup
{
    public static function create(string $prefix, string $controller): RouteCollectorInterface
    {
        $routes = self::createDefaultRoutes($prefix, $controller);
        $group = Group::create($prefix, $routes);

        // add middlewares here

        return $group;
    }

    private static function createDefaultRoutes(string $prefix, string $controller): array
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
                $routes[] = Route::methods([$httpMethod], $prefix, [$controller, $methodName]);
            }
        }

        return $routes;
    }
}
