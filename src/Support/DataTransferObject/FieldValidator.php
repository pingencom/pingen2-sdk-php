<?php

declare(strict_types=1);

namespace Pingen\Support\DataTransferObject;

use ReflectionProperty;

abstract class FieldValidator
{
    public bool $isNullable;

    public bool $isMixed;

    public bool $isMixedArray;

    public bool $hasDefaultValue;

    public array $allowedTypes;

    public array $allowedArrayTypes;

    public array $allowedArrayKeyTypes;

    protected static array $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    protected bool $hasTypeDeclaration;

    public static function fromReflection(ReflectionProperty $property): self
    {
        $docDefinition = null;

        if ($property->getDocComment()) {
            preg_match(
                DocblockFieldValidator::DOCBLOCK_REGEX,
                $property->getDocComment(),
                $matches
            );

            $docDefinition = $matches[0] ?? null;
        }

        if ($docDefinition) {
            return new DocblockFieldValidator($docDefinition, $property->isDefault());
        }

        return new PropertyFieldValidator($property);
    }

    public function isValidType(mixed $value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

        if ($this->isMixed) {
            return true;
        }

        if (is_iterable($value) && $this->isMixedArray) {
            return true;
        }

        if ($this->isNullable && $value === null) {
            return true;
        }

        if (is_iterable($value)) {
            return $this->isValidIterable($value);
        }

        return $this->isValidValue($value);
    }

    private function isValidIterable(iterable $iterable): bool
    {
        // If the iterable matches one of the normal types, we immediately return true
        // For example: custom collection classes type hinted with `MyCollection`
        $isValidValue = $this->isValidValue($iterable);

        if ($isValidValue) {
            return true;
        }

        // If not, we'll check all individual iterable items and keys
        foreach ($iterable as $key => $value) {
            $isValidValue = false;

            // First we check whether the value matches the value type definition
            foreach ($this->allowedArrayTypes as $type) {
                $isValidValue = $this->assertValidType($type, $value);

                // No need to further check this value when a valid type is found
                if ($isValidValue) {
                    break;
                }
            }

            // If a value is invalid, we immediately return false
            if (! $isValidValue) {
                return false;
            }

            // We'll assume keys are valid by default, since they can be omitted
            $isValidKey = true;

            // Next we check the key's value
            foreach ($this->allowedArrayKeyTypes as $keyType) {
                $isValidKey = $this->assertValidType($keyType, $key);

                // No need to further check this jey when a valid type is found
                if ($isValidKey) {
                    break;
                }
            }

            // If a key type is invalid, we'll immediately return
            if (! $isValidKey) {
                return false;
            }

            // Moving on to checking the next $key => $value pair
        }

        // If value and key type checks pass, we can return true
        return true;
    }

    private function isValidValue(mixed $value): bool
    {
        foreach ($this->allowedTypes as $type) {
            // We'll check the type of this value against all allowed types, if one matches we're good
            $isValidType = $this->assertValidType($type, $value);

            if ($isValidType) {
                return true;
            }
        }

        return false;
    }

    private function assertValidType(string $type, mixed $value): bool
    {
        return $value instanceof $type || gettype($value) === $type;
    }
}
