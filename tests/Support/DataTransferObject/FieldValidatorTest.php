<?php

declare(strict_types=1);

namespace Tests\Support\DataTransferObject;

use Pingen\Support\DataTransferObject\DocblockFieldValidator;
use Pingen\Support\DataTransferObject\ValueCaster;
use Tests\TestCase;

class FieldValidatorTest extends TestCase
{
    public function testValueWithoutTypeDeclaration(): void
    {
        $validator = new DocblockFieldValidator('');

        $this->assertTrue($validator->isNullable);
        $this->assertTrue($validator->isValidType('anything'));
        $this->assertTrue($validator->isValidType(42));
    }

    public function testMixedValue(): void
    {
        $validator = new DocblockFieldValidator('@var mixed');

        $this->assertTrue($validator->isMixed);
        $this->assertTrue($validator->isValidType(42));
    }

    public function testMixedArray(): void
    {
        $this->assertTrue((new DocblockFieldValidator('@var iterable'))->isMixedArray);
        $this->assertTrue((new DocblockFieldValidator('@var array'))->isMixedArray);
    }

    public function testGenericArrayTypes(): void
    {
        $validator = new DocblockFieldValidator('@var array<string, int>');

        $this->assertSame(['integer'], $validator->allowedArrayTypes);
        $this->assertSame(['string'], $validator->allowedArrayKeyTypes);
    }

    public function testValidArrayKey(): void
    {
        $validator = new DocblockFieldValidator('@var array<string, int>');

        $this->assertTrue($validator->isValidType(['label' => 1]));
    }

    public function testInvalidArrayKey(): void
    {
        $validator = new DocblockFieldValidator('@var array<string, int>');

        $this->assertFalse($validator->isValidType([0 => 1]));
    }

    public function testArrayTypedList(): void
    {
        $this->assertSame(['string'], (new DocblockFieldValidator('@var string[]'))->allowedArrayTypes);
    }

    public function testCastCollectionWithoutDto(): void
    {
        $caster = new ValueCaster();
        $values = [['a' => 1], ['b' => 2]];

        $this->assertSame($values, $caster->castCollection($values, ['string']));
    }
}
