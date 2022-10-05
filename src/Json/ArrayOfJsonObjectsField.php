<?php

namespace JsonFieldCast\Json;

use Illuminate\Database\Eloquent\Model;

class ArrayOfJsonObjectsField implements \JsonSerializable, \ArrayAccess, \Iterator, \Countable
{
    protected Model $model;

    /**
     * JsonObject[]
     */
    protected array $data;

    protected int $iteratorPosition = 0;

    public function __construct(Model $model, array $data = [])
    {
        $this->iteratorPosition = 0;
        $this->data             = array_values(array_filter(array_map(function ($item) {
            if ($item instanceof JsonObject) {
                return $item;
            }
            if (is_array($item)) {
                return new JsonObject($item);
            }

            return null;
        }, $data)));
        $this->model = $model;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function current(): mixed
    {
        return $this->data[$this->iteratorPosition];
    }

    public function next(): void
    {
        ++$this->iteratorPosition;
    }

    public function key(): mixed
    {
        return $this->iteratorPosition;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->iteratorPosition]);
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    public function jsonSerialize(): mixed
    {
        return array_values(array_filter(array_map(function ($item) {
            if ($item instanceof JsonObject) {
                return $item->jsonSerialize();
            }

            return null;
        }, $this->data)));
    }
}
