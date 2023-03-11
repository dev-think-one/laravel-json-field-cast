<?php

namespace JsonFieldCast\Json;

trait HasNumericAttributes
{
    public function increment(string $key, int|float $amount = 1): static
    {
        $value = $this->getAttribute($key);

        if ($value && !is_numeric($value)) {
            throw new \InvalidArgumentException("Value of key [{$key}] is not numeric");
        }

        if (!$value) {
            $value = 0;
        }

        return $this->setAttribute($key, $value + $amount);
    }

    public function decrement(string $key, int|float $amount = 1): static
    {
        $value = $this->getAttribute($key);

        if ($value && !is_numeric($value)) {
            throw new \InvalidArgumentException("Value of key [{$key}] is not numeric");
        }

        if (!$value) {
            $value = 0;
        }

        return $this->setAttribute($key, $value - $amount);
    }
}
