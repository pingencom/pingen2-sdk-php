<?php

namespace Pingen\Support\DataTransferObject;

class ValueCaster
{
    public function cast(mixed $value, FieldValidator $validator): mixed
    {
        return $this->shouldBeCastToCollection($value)
            ? $this->castCollection($value, $validator->allowedArrayTypes)
            : $this->castValue($value, $validator->allowedTypes);
    }

    /**
     * @param mixed $value
     * @param array $allowedTypes
     * @return mixed|DataTransferObject
     */
    public function castValue(mixed $value, array $allowedTypes)
    {
        $castTo = null;

        foreach ($allowedTypes as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $value;
        }

        return new $castTo($value);
    }

    public function castCollection(mixed $values, array $allowedArrayTypes): mixed
    {
        $castTo = null;

        foreach ($allowedArrayTypes as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $values;
        }

        $casts = [];

        foreach ($values as $value) {
            $casts[] = new $castTo($value);
        }

        return $casts;
    }

    public function shouldBeCastToCollection(array $values): bool
    {
        if (empty($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                return false;
            }

            if (! is_array($value)) {
                return false;
            }
        }

        return true;
    }
}
