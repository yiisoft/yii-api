<?php

namespace Yiisoft\Yii\Rest\Tests\Support;

class TestModel
{
    public static $fields = ['field1', 'field2'];
    public static $extraFields = [];

    public $field1 = 'test';
    public $field2 = 2;
    public $extraField1 = 'testExtra';
    public $extraField2 = 42;
    public $extraField3;

    public function fields()
    {
        return static::$fields;
    }

    public function extraFields()
    {
        return static::$extraFields;
    }
}
