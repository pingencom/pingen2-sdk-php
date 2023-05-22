<?php

declare(strict_types=1);

namespace Tests\Support\DataTransferObject;

use Pingen\Endpoints\DataTransferObjects\Letter\LetterAttributes;
use Pingen\Support\DataTransferObject\DataTransferObject;
use Pingen\Support\DataTransferObject\DataTransferObjectError;
use Tests\TestCase;

class DataTransferObjectTest extends TestCase
{
    public function testUninitialized(): void
    {
        try {
            new DummyDTO();
        } catch (DataTransferObjectError $e) {
            $this->assertEquals("The following invalid types were encountered:\nexpected `Tests\Support\DataTransferObject\DummyDTO::status` to be of type `string`, instead got value `null`, which is NULL.\nexpected `Tests\Support\DataTransferObject\DummyDTO::name` to be of type `string`, instead got value `null`, which is NULL.\n", $e->getMessage());
        }
    }

    public function testInvalidTypeMessage(): void
    {
        try {
            new DummyDTO([
                'status' => 'sent',
                'name' => 1
            ]);
        } catch (DataTransferObjectError $e) {
            $this->assertEquals("Invalid type: expected `Tests\Support\DataTransferObject\DummyDTO::name` to be of type `string`, instead got value `1`, which is integer..", $e->getMessage());
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
        $dto = new DummyDTO([
            'status' => 'sent',
            'name' => 'name'
        ]);

        $this->assertSame([
            'status' => 'sent',
            'name' => 'name',
            'price' => null
        ], $dto->all());
    }
}

class DummyDTO extends DataTransferObject
{
    public string $status;
    public string $name;
    public ?int $price;

    protected bool $ignoreMissing = false;
}