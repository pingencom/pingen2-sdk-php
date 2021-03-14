<?php

declare(strict_types=1);

namespace Pingen\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

        return null;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];

        collect($this->touchedBySetter)
            ->each(function ($property) use (&$data): void {
                $data[$property] = $this->{$property};
            });

        return $data;
    }
}
