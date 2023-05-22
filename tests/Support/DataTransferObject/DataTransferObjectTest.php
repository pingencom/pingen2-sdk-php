<?php

declare(strict_types=1);

namespace Tests\Support\DataTransferObject;

use Pingen\Support\DataTransferObject\DataTransferObject;
use Pingen\Support\DataTransferObject\DataTransferObjectError;
use Tests\TestCase;

class DataTransferObjectTest extends TestCase
{
    public function testInvalidTypeMessage(): void
    {
        try {
            new DummyDTO([
                'status' => 'sent',
                'name' => ['test' => 'example']
            ]);
        } catch (DataTransferObjectError $e) {
            $this->assertEquals("Invalid type: expected `Tests\Support\DataTransferObject\DummyDTO::name` to be of type `string`, instead got value `array`..", $e->getMessage());
        }
    }

    public function testInvalidTypesMessage(): void
    {
        try {
            new DummyDTO([
                'status' => null,
                'name' => 1,
                'object' => 'test',
                'array' => (object)['test'],
                'array_access' => 1
            ]);
        } catch (DataTransferObjectError $e) {
            $this->assertEquals("The following invalid types were encountered:\nexpected `Tests\Support\DataTransferObject\DummyDTO::status` to be of type `string`, instead got value `null`, which is NULL.\nexpected `Tests\Support\DataTransferObject\DummyDTO::name` to be of type `string`, instead got value `1`, which is integer.\nexpected `Tests\Support\DataTransferObject\DummyDTO::object` to be of type `object`, instead got value `test`, which is string.\nexpected `Tests\Support\DataTransferObject\DummyDTO::array` to be of type `array`, instead got value `stdClass`, which is object.\nexpected `Tests\Support\DataTransferObject\DummyDTO::array_access` to be of type `ArrayAccess`, instead got value `1`, which is integer.\n", $e->getMessage());
        }
    }

    public function testUnknownProperties(): void
    {
        try {
            new DummyDTO([
                'status' => 'sent',
                'name' => 'name',
                'something' => 1
            ]);
        } catch (DataTransferObjectError $e) {
            $this->assertEquals("Public properties `something` not found on Tests\Support\DataTransferObject\DummyDTO", $e->getMessage());
        }
    }

    public function testGetAll(): void
    {
        $arr = new \ArrayObject(['example']);
        $dto = new DummyDTO([
            'status' => 'sent',
            'name' => 'name',
            'array' => ['test' => 'example'],
            'array_access' => $arr
        ]);

        $this->assertSame([
            'status' => 'sent',
            'name' => 'name',
            'price' => null,
            'object' => null,
            'array' => ['test' => 'example'],
            'array_access' => $arr,
            'mixed' => null
        ], $dto->all());
    }
}

class DummyDTO extends DataTransferObject
{
    public string $status;
    public string $name;
    public ?int $price;
    public ?object $object;
    public ?array $array;
    public ?\ArrayAccess $array_access;
    public mixed $mixed;

    protected bool $ignoreMissing = false;
}