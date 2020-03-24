<?php

namespace Yiisoft\Yii\Rest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Method;
use Yiisoft\Router\Route;
use Yiisoft\Yii\Rest\RestGroup;
use Yiisoft\Yii\Rest\Tests\Support\Controller\GetController;
use Yiisoft\Yii\Rest\Tests\Support\Controller\TestController;

final class RestGroupTest extends TestCase
{
    public function testCreateDefaultRoutes(): void
    {
        $group = RestGroup::create('users', TestController::class);
        $routes = $group->getItems();
        $this->assertCount(8, $routes);

        $methodsToCheck = [
            'get' => Method::GET,
            'list' => Method::GET,
            'post' => Method::POST,
            'put' => Method::PUT,
            'delete' => Method::DELETE,
            'patch' => Method::PATCH,
            'head' => Method::HEAD,
            'options' => Method::OPTIONS,
        ];
        foreach ($routes as $route) {
            $this->assertInstanceOf(Route::class, $route);
            $methods = $route->getMethods();
            $this->assertCount(1, $methods);
            $method = current($methods);
            $this->assertContains($method, $methodsToCheck);
            unset($methodsToCheck[array_search($method, $methodsToCheck, true)]);
        }
        $this->assertEmpty($methodsToCheck);
    }

    public function testCreateOnlyExistsMethodsRoutes(): void
    {
        $group = RestGroup::create('users', GetController::class);
        $routes = $group->getItems();

        $this->assertCount(1, $routes);
        $route = current($routes);
        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame([Method::GET], $route->getMethods());
    }
}
