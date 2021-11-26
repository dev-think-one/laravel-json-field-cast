<?php

namespace JsonFieldCast\Json;

use Illuminate\Support\Arr;

/**
 * @property array $data
 */
trait HasDataArrayWithAttributes
{

    public function setData(array $data = []): static
    {
        $this->data = $data;

        return $this;
    }

    public function getRawData(array|string|null $keys = null): array
    {
        if (is_null($keys)) {
            return $this->data;
        }
        $keys = (array) $keys;

        return Arr::only($this->data, $keys);
    }

    public function getRawDataExcept(array|string $keys = []): array
    {
        $keys = (array) $keys;

        if (!empty($keys)) {
            return array_diff_key($this->data, array_flip($keys));
        }

        return $this->data;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->data, $key, $default);
    }

    public function hasAttribute(string $key): bool
    {
        return Arr::has($this->data, $key);
    }

    public function setAttribute(string $key, $value): static
    {
        Arr::set($this->data, $key, $value);

        return $this;
    }

    public function removeAttribute(string $key): static
    {
        Arr::forget($this->data, $key);

        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
