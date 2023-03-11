<?php

namespace JsonFieldCast\Json;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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
        $keys = (array)$keys;

        return Arr::only($this->data, $keys);
    }

    public function getRawDataExcept(array|string $keys = []): array
    {
        $keys = (array)$keys;

        if (!empty($keys)) {
            return array_diff_key($this->data, array_flip($keys));
        }

        return $this->data;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
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

    public function getDateAttribute(string $key, ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            try {
                return Carbon::parse($value);
            } catch (InvalidFormatException) {
            }
        }

        return $default;
    }

    public function getDateTimeFromFormat(string $key, string $format = 'Y-m-d H:i:s', ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            return Carbon::createFromFormat($format, $value);
        }

        return $default;
    }

    public function getDateTimeFromFormats(string $key, string|array $formats = 'Y-m-d H:i:s', ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            $formats = Arr::wrap($formats);
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (InvalidFormatException $e) {
                }
            }
            if (isset($e) && ($e instanceof InvalidFormatException)) {
                throw $e;
            }
        }

        return $default;
    }

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

    public function toMorph(string $key, Model $value, string $idKeyName = 'id', string $classKeyName = 'class'): static
    {
        $this->setAttribute($key ? "{$key}.{$idKeyName}" : $idKeyName, $value->getKey());
        $this->setAttribute($key ? "{$key}.{$classKeyName}" : $classKeyName, $value->getMorphClass());

        return $this;
    }

    public function fromMorph(string $key = '', ?Model $default = null, string $idKeyName = 'id', string $classKeyName = 'class'): ?Model
    {
        $id    = $this->getAttribute($key ? "{$key}.{$idKeyName}" : $idKeyName);
        $class = $this->getAttribute($key ? "{$key}.{$classKeyName}" : $classKeyName);
        if (!$id || !$class) {
            return $default;
        }

        $class = Relation::getMorphedModel($class) ?? $class;
        if (!is_a($class, Model::class, true)) {
            return $default;
        }

        $model = $class::find($id);
        if (!$model) {
            return $default;
        }

        return $model;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
