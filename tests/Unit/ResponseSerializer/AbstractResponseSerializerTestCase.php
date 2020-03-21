<?php

namespace Yiisoft\Yii\Rest\Tests\Unit\ResponseSerializer;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Rest\ResponseSerializerInterface;

abstract class AbstractResponseSerializerTestCase extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     * @param array $data
     */
    public function testSerialize(...$data): void
    {
        $serializer = $this->getSerializer();

        $response = $serializer->serialize(123, $data);

        $this->assertResultContainData($response->getBody()->getContents(), $data);
    }

    abstract protected function getSerializer(): ResponseSerializerInterface;

    private function assertResultContainData(string $content, $data): void
    {
        if (is_string($data) || is_int($data)) {
            $this->assertStringContainsString($data, $content);

            return;
        }

        if (is_array($data)) {
            foreach ($data as $datum) {
                $this->assertResultContainData($content, $datum);
            }

            return;
        }

        if (is_object($data)) {
            foreach (get_object_vars($data) as $property => $value) {
                $this->assertResultContainData($content, $property);
                $this->assertResultContainData($content, $value);
            }

            return;
        }
    }

    public function serializeDataProvider(): array
    {
        $class = new \stdClass();
        $class->intProperty = 55555;
        $class->stringProperty = 'string';
        $class->objectProperty = new \stdClass();

        return [
            ['data'],
            [[[[[['data']]]]]],
            [1, 2, 3],
            [$class],
        ];
    }
}
