<?php

declare(strict_types=1);

namespace Pingen\Support\DataTransferObject;

use TypeError;

class DataTransferObjectError extends TypeError
{
    public static function unknownProperties(array $properties, string $className): self
    {
        $propertyNames = implode('`, `', $properties);

        return new self("Public properties `{$propertyNames}` not found on {$className}");
    }

    public static function invalidTypes(array $invalidTypes): self
    {
        $msg = count($invalidTypes) > 1
            ? "The following invalid types were encountered:\n" . implode("\n", $invalidTypes) . "\n"
            : "Invalid type: {$invalidTypes[0]}.";

        throw new self($msg);
    }

    public static function invalidTypeMessage(
        string $class,
        string $field,
        array $expectedTypes,
        mixed $value
    ): string {
        $currentType = gettype($value);

        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        if (is_array($value)) {
            $value = 'array';
        }

        $expectedTypes = implode(', ', $expectedTypes);

        if ($value === $currentType) {
            $instead = "instead got value `{$value}`.";
        } else {
            $instead = "instead got value `{$value}`, which is {$currentType}.";
        }

        return "expected `{$class}::{$field}` to be of type `{$expectedTypes}`, {$instead}";
    }

    public static function uninitialized(string $class, string $field): self
    {
        return new self("Non-nullable property `{$class}::{$field}` has not been initialized.");  // @codeCoverageIgnore
    }
}
