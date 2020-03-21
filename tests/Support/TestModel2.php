<?php

namespace Yiisoft\Yii\Rest\Tests\Support;

class TestModel2
{
    public static $fields = ['field3', 'field4'];
    public static $extraFields = [];

    public $field3 = 'test2';
    public $field4 = 8;
    public $extraField4 = 'testExtra2';

    public function fields()
    {
        return static::$fields;
    }

    public function extraFields()
    {
        return static::$extraFields;
    }
}
