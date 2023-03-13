<?php

namespace Pingen\Support\DataTransferObject;

class Str
{
    public static function contains(string $string, mixed $searches): bool
    {
        $searches = (array) $searches;

        foreach ($searches as $search) {
            if (str_contains($string, $search)) {
                return true;
            }
        }

        return false;
    }
}
