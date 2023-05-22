<?php

declare(strict_types=1);

namespace Pingen\Support\DataTransferObject;

use ArrayAccess;

class Arr
{
    /**
     * @param array<mixed> $array
     * @param string[] $keys
     * @return array<mixed>
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * @param mixed $array
     * @param string[] $keys
     * @return array<mixed>
     */
    public static function except(mixed $array, array $keys): array
    {
        return static::forget($array, $keys);
    }

    /**
     * @param array<mixed> $array
     * @param string[] $keys
     * @return array<mixed>
     */
    public static function forget(mixed $array, array $keys): array
    {
        if (count($keys) === 0) {
            return $array;
        }

        foreach ($keys as $key) {
            // If the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part]; // @codeCoverageIgnore
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }

        return $array;
    }

    public static function exists(mixed $array, string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key); // @codeCoverageIgnore
        }

        return array_key_exists($key, $array);
    }
}
