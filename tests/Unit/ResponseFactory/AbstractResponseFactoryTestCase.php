<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Rest\Tests\Unit\ResponseFactory;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Rest\ResponseFactoryInterface;

abstract class AbstractResponseFactoryTestCase extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     * @param array $data
     */
    public function testSerialize(...$data): void
    {
        $serializer = $this->getFactory();

        $response = $serializer->createResponse($data);

        $stream = $response->getBody();
        $stream->rewind();

        $this->assertResultContainData($stream->getContents(), $data);
    }

    abstract protected function getFactory(): ResponseFactoryInterface;

    private function assertResultContainData(string $content, $data): void
    {
        if (is_string($data) || is_int($data)) {
            $this->assertStringContainsString((string)$data, $content);

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
