<?php

namespace JsonFieldCast\Json;

use Illuminate\Support\Arr;

/**
 * @property array $data
 */
trait HasDataArrayWithAttributes
{

    /**
     * @param array $data
     *
     * @return static
     */
    public function setData(array $data = []): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getRawData(array $keys = []): array
    {
        if (!empty($keys)) {
            return Arr::only($this->data, $keys);
        }

        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getAttribute(string $key, mixed $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return Arr::has($this->data, $key);
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute(string $key, $value): static
    {
        Arr::set($this->data, $key, $value);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute(string $key): static
    {
        Arr::forget($this->data, $key);

        return $this;
    }

    /**
     * @return array
     */
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
