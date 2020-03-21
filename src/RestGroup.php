<?php

namespace Yiisoft\Yii\Rest;

use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;

class RestGroup
{
    public static function create(string $prefix, $controller): RouteCollectorInterface
    {
        $routes = self::createDefaultRoutes($prefix, $controller);
        $group = Group::create($prefix, $routes);

        // add middlewares here

        return $group;
    }

    private static function createDefaultRoutes(string $prefix, $controller): array
    {
        return [
            Route::delete($prefix, [$controller, 'actionDelete']),
            Route::head($prefix, [$controller, 'actionHead']),
            Route::get($prefix, [$controller, 'actionGet']),
            Route::options($prefix, [$controller, 'actionOptions']),
            Route::patch($prefix, [$controller, 'actionPatch']),
            Route::post($prefix, [$controller, 'actionPost']),
            Route::put($prefix, [$controller, 'actionPut']),
        ];
    }
}
