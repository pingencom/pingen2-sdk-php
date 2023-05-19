<?php

declare(strict_types=1);

namespace Pingen\Support\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class DataTransferObject
 * @package Pingen\Support\DataTransferObject
 */
abstract class DataTransferObject
{
    /**
     * - The key is the type of the property
     * - The callable receives the value and may return the same or a different value
     * @var array<string,callable>
     */
    public static array $makers = [];

    protected bool $ignoreMissing = true;

    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    /**
     * @param array<mixed> $parameters
     */
    final public function __construct(array $parameters = [])
    {
        $validators = $this->getFieldValidators();
        $valueCaster = $this->getValueCaster();

        /** string[] */
        $invalidTypes = [];

        foreach ($validators as $field => $validator) {
            if (
                ! isset($parameters[$field])
                && ! $validator->hasDefaultValue
                && ! $validator->isNullable
            ) {
                throw DataTransferObjectError::uninitialized(
                    static::class,
                    $field
                );
            }

            $value = $parameters[$field] ?? $this->{$field} ?? null;

            $value = $this->castValue($valueCaster, $validator, $value);

            if (! $validator->isValidType($value)) {
                $invalidTypes[] = DataTransferObjectError::invalidTypeMessage(
                    static::class,
                    $field,
                    $validator->allowedTypes,
                    $value
                );

                continue;
            }

            $this->{$field} = $value;

            unset($parameters[$field]);
        }

        if ($invalidTypes) {
            DataTransferObjectError::invalidTypes($invalidTypes);
        }

        if (! $this->ignoreMissing && count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
        }
    }

    /**
     * @return array<mixed>
     */
    public function all(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
        }

        return $data;
    }

    public function only(string ...$keys): self
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $dataTransferObject;
    }

    public function except(string ...$keys): self
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = [...$this->exceptKeys, ...$keys];

        return $dataTransferObject;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        return $this->parseArray($array);
    }

    protected function parseArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }

    /**
     * @return FieldValidator[]
     */
    protected function getFieldValidators(): array
    {
        return DTOCache::resolve(static::class, function () {
            $class = new ReflectionClass(static::class);

            $properties = [];

            foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
                // Skip static properties
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                $field = $reflectionProperty->getName();

                $properties[$field] = FieldValidator::fromReflection($reflectionProperty);
            }

            return $properties;
        });
    }

    protected function castValue(ValueCaster $valueCaster, FieldValidator $fieldValidator, mixed $value): mixed
    {
        if (is_array($value)) {
            return $valueCaster->cast($value, $fieldValidator);
        }

        return $this->makeValue($fieldValidator, $value);
    }

    protected function makeValue(FieldValidator $fieldValidator, mixed $value): mixed
    {
        foreach ($fieldValidator->allowedTypes as $type) {
            $type = ltrim($type, '\\');

            if (! isset(self::$makers[$type])) {
                continue;
            }

            return self::$makers[$type]($value);
        }

        return $value;
    }

    protected function getValueCaster(): ValueCaster
    {
        return new ValueCaster();
    }
}
