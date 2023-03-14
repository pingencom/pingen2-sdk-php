<?php

declare(strict_types=1);

namespace Pingen\Support\DataTransferObject;

use ArrayAccess;
use Countable;
use Iterator;

abstract class DataTransferObjectCollection implements
    ArrayAccess,
    Iterator,
    Countable
{
    protected array $collection;

    protected int $position = 0;

    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    public function current(): mixed
    {
        return $this->collection[$this->position];
    }

    public function offsetGet($offset): mixed
    {
        return $this->collection[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->collection);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function toArray(): array
    {
        $collection = $this->collection;

        foreach ($collection as $key => $item) {
            if (
                ! $item instanceof DataTransferObject
                && ! $item instanceof self
            ) {
                continue;
            }

            $collection[$key] = $item->toArray();
        }

        return $collection;
    }

    public function items(): array
    {
        return $this->collection;
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
