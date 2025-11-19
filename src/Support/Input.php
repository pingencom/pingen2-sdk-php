<?php

declare(strict_types=1);

namespace Pingen\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pingen\Exceptions\ValidationException;
use Pingen\Support\DataTransferObject\FieldValidator;

/**
 * Class Input
 * @package Pingen\Support
 */
abstract class Input implements Arrayable
{
    /** @var array<mixed> */
    private array $touchedBySetter = [];

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @link https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    public function __call($name, $arguments)
    {
        if (Str::of($name)->contains('set')) {
            $name = Str::of($name)
                ->replace('set', '')
                ->snake();

            $this->{$name} = Arr::first($arguments);

            $this->touchedBySetter[] = (string) $name;

            return $this;
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        collect($this->touchedBySetter)
            ->each(function ($property) use (&$data): void {
                $propertyValue = $this->{$property};

                if ($this->{$property} instanceof Input) {
                    $propertyValue = $this->{$property}->toArray();
                }

                $data[$property] = $propertyValue;
            });

        return $data;
    }

    /**
     * @param string[] $excludedParameters
     * @return void
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function validate(array $excludedParameters = []): void
    {
        $attributes = $this->toArray();
        $properties = [];
        $errorMsg = [];

        $class = new \ReflectionClass($this::class);

        foreach ($class->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            $field = $reflectionProperty->getName();

            $properties[$field] = FieldValidator::fromReflection($reflectionProperty);
        }

        foreach ($properties as $field => $validator) {
            if (! isset($attributes[$field]) && ! $validator->isNullable) {
                if (in_array($field, $excludedParameters, true)) {
                    continue;
                }

                $errorMsg[] = sprintf('The %s field is required.', $field);
                continue;
            }

            if (! $validator->isNullable && ! in_array(gettype($attributes[$field]), $validator->allowedTypes, true)) {
                $errorMsg[] = sprintf(
                    'The %s field has wrong type %s possible type: %s.',
                    $field,
                    gettype($attributes[$field]),
                    json_encode(
                        $validator->allowedTypes
                    )
                );
            }
        }

        if (count($errorMsg) > 0) {
            throw new ValidationException((string) json_encode($errorMsg));
        }
    }
}
