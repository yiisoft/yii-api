<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Rest\Serializer;

class SerializerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TestModel::$fields = ['field1', 'field2'];
        TestModel::$extraFields = [];
    }

    public function testSerializeModelErrors()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();

        $model->addError('field1', 'Test error');
        $model->addError('field2', 'Multiple error 1');
        $model->addError('field2', 'Multiple error 2');

        $this->assertEquals([
            [
                'field' => 'field1',
                'message' => 'Test error',
            ],
            [
                'field' => 'field2',
                'message' => 'Multiple error 1',
            ],
        ], $serializer->serialize($model));
    }

    public function testSerializeModelData()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();

        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        TestModel::$fields = ['field1'];
        TestModel::$extraFields = [];

        $this->assertSame([
            'field1' => 'test',
        ], $serializer->serialize($model));

        TestModel::$fields = ['field1'];
        TestModel::$extraFields = ['field2'];

        $this->assertSame([
            'field1' => 'test',
        ], $serializer->serialize($model));
    }

    public function testExpand()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();

        TestModel::$fields = ['field1', 'field2'];
        TestModel::$extraFields = ['extraField1'];

        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['expand' => 'extraField1']);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
            'extraField1' => 'testExtra',
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['expand' => 'extraField1,extraField2']);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
            'extraField1' => 'testExtra',
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['expand' => 'field1,extraField2']);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));
    }

    public function testNestedExpand()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();
        $model->extraField3 = new TestModel2();

        TestModel::$extraFields = ['extraField3'];
        TestModel2::$extraFields = ['extraField4'];

        $this->app->request->setQueryParams(['expand' => 'extraField3.extraField4']);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
            'extraField3' => [
                'field3' => 'test2',
                'field4' => 8,
                'extraField4' => 'testExtra2',
            ],
        ], $serializer->serialize($model));
    }

    public function testFields()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();
        $model->extraField3 = new TestModel2();

        TestModel::$extraFields = ['extraField3'];

        $this->app->request->setQueryParams([]);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['fields' => '*']);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(
            [
                'fields' => 'field1,extraField3.field3',
                'expand' => 'extraField3.extraField4',
            ]
        );
        $this->assertSame([
            'field1' => 'test',
            'extraField3' => [
                'field3' => 'test2',
                'extraField4' => 'testExtra2',
            ],
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(
            [
                'fields' => 'extraField3.*',
                'expand' => 'extraField3',
            ]
        );
        $this->assertSame([
            'extraField3' => [
                'field3' => 'test2',
                'field4' => 8,
            ],
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(
            [
                'fields' => 'extraField3.*',
                'expand' => 'extraField3.extraField4',
            ]
        );
        $this->assertSame([
            'extraField3' => [
                'field3' => 'test2',
                'field4' => 8,
                'extraField4' => 'testExtra2',
            ],
        ], $serializer->serialize($model));

        $model->extraField3 = [
            new TestModel2(),
            new TestModel2(),
        ];

        $this->app->request->setQueryParams(
            [
                'fields' => 'extraField3.*',
                'expand' => 'extraField3',
            ]
        );
        $this->assertSame([
            'extraField3' => [
                [
                    'field3' => 'test2',
                    'field4' => 8,
                ],
                [
                    'field3' => 'test2',
                    'field4' => 8,
                ],
            ],
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(
            [
                'fields' => '*,extraField3.*',
                'expand' => 'extraField3',
            ]
        );
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
            'extraField3' => [
                [
                    'field3' => 'test2',
                    'field4' => 8,
                ],
                [
                    'field3' => 'test2',
                    'field4' => 8,
                ],
            ],
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(
            [
                'fields' => 'extraField3.field3',
                'expand' => 'extraField3',
            ]
        );
        $this->assertSame([
            'extraField3' => [
                ['field3' => 'test2'],
                ['field3' => 'test2'],
            ],
        ], $serializer->serialize($model));
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/12107
     */
    public function testExpandInvalidInput()
    {
        $serializer = $this->getSerializer();
        $model = new TestModel();

        $this->app->request->setQueryParams(['expand' => ['field1,extraField2']]);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['fields' => ['field1,extraField2']]);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));

        $this->app->request->setQueryParams(['fields' => ['field1,extraField2'], 'expand' => ['field1,extraField2']]);
        $this->assertSame([
            'field1' => 'test',
            'field2' => 2,
        ], $serializer->serialize($model));
    }

    public function dataProviderSerializeDataProvider()
    {
        $adp1 = new ArrayDataProvider();
        $adp1->allModels = [
            ['id' => 1, 'username' => 'Bob'],
            ['id' => 2, 'username' => 'Tom'],
        ];
        $adp1->setPagination([
            'route' => '/',
        ]);

        $adp2 = new ArrayDataProvider();
        $adp2->allModels = [
            ['id' => 1, 'username' => 'Bob'],
            ['id' => 2, 'username' => 'Tom'],
        ];
        $adp2->setPagination([
            'route' => '/',
            'pageSize' => 1,
            'page' => 0,
        ]);

        $adp3 = new ArrayDataProvider();
        $adp3->allModels = [
            ['id' => 1, 'username' => 'Bob'],
            ['id' => 2, 'username' => 'Tom'],
        ];
        $adp3->setPagination([
            'route' => '/',
            'pageSize' => 1,
            'page' => 1,
        ]);

        $adp4 = new ArrayDataProvider();
        $adp4->allModels = [
            'Bob' => ['id' => 1, 'username' => 'Bob'],
            'Tom' => ['id' => 2, 'username' => 'Tom'],
        ];
        $adp4->setPagination([
            'route' => '/',
            'pageSize' => 1,
            'page' => 1,
        ]);

        return [
            [
                $adp1,
                [
                    ['id' => 1, 'username' => 'Bob'],
                    ['id' => 2, 'username' => 'Tom'],
                ],
            ],
            [
                $adp2,
                [
                    ['id' => 1, 'username' => 'Bob'],
                ],
            ],
            [
                $adp3,
                [
                    ['id' => 2, 'username' => 'Tom'],
                ],
            ],
            [
                $adp4,
                [
                    ['id' => 2, 'username' => 'Tom'],
                ],
            ],
            [
                $adp3,
                [
                    1 => ['id' => 2, 'username' => 'Tom'],
                ],
                true,
            ],
            [
                $adp4,
                [
                    'Tom' => ['id' => 2, 'username' => 'Tom'],
                ],
                true,
            ],
            /*[
                new ArrayDataProvider([
                    'allModels' => [
                        new \DateTime('2000-01-01'),
                    ],
                    'pagination' => [
                        'route' => '/',
                    ],
                ]),
                [
                    [
                        'date' => '2000-01-01 00:00:00.000000',
                        'timezone_type' => 3,
                        'timezone' => 'UTC',
                    ],
                ]
            ],*/
        ];
    }

    /**
     * @dataProvider dataProviderSerializeDataProvider
     * @param \yii\data\DataProviderInterface $dataProvider
     * @param array $expectedResult
     * @param bool $saveKeys
     */
    public function testSerializeDataProvider($dataProvider, $expectedResult, $saveKeys = false)
    {
        $serializer = $this->getSerializer();
        $serializer->preserveKeys = $saveKeys;

        $this->assertEquals($expectedResult, $serializer->serialize($dataProvider));
    }

    private function getSerializer()
    {
        return new Serializer($this->container->get('request'), $this->container->get('response'));
    }
}

