<?php

declare(strict_types=1);

namespace Tests\Support;

use Pingen\Exceptions\ValidationException;
use Pingen\Support\Input;
use Tests\TestCase;

class InputTest extends TestCase
{
    public function testValidateWrongType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The label field has wrong type integer');

        (new TypeMismatchInput())
            ->setLabel(123)
            ->validate();
    }

    public function testValidatePasses(): void
    {
        $this->expectNotToPerformAssertions();

        (new TypeMismatchInput())
            ->setLabel('correct')
            ->validate();
    }
}

class TypeMismatchInput extends Input
{
    /** @var string */
    protected $label;
}
