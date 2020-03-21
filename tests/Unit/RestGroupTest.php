<?php

namespace Yiisoft\Yii\Rest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Method;
use Yiisoft\Router\Route;
use Yiisoft\Yii\Rest\RestGroup;
use Yiisoft\Yii\Rest\Tests\Support\TestController;

class RestGroupTest extends TestCase
{
    public function testCreateDefaultRoutes(): void
    {
        $group = RestGroup::create('users', TestController::class);
        $routes = $group->getItems();
        $this->assertCount(7, $routes);

        $methodsToCheck = Method::ANY;
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
}
