<?php

namespace Pingen\Support\DataTransferObject;

class ImmutableDataTransferObject
{
    protected DataTransferObject $dataTransferObject;

    public function __construct(DataTransferObject $dataTransferObject)
    {
        foreach (get_object_vars($dataTransferObject) as $k => $v) {
            if (is_subclass_of($v, DataTransferObject::class)) {
                $dataTransferObject->{$k} = new self($v); // @phpstan-ignore-line
            }
        }
        $this->dataTransferObject = $dataTransferObject;
    }

    public function __set(string $name, mixed $value): void
    {
        throw DataTransferObjectError::immutable($name);
    }

    public function __get(string $name): mixed
    {
        return $this->dataTransferObject->{$name};
    }

    public function __call(mixed $name, mixed $arguments): mixed
    {
        return call_user_func_array([$this->dataTransferObject, $name], $arguments); // @phpstan-ignore-line
    }
}
