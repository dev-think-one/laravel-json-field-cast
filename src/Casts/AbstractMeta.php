<?php


namespace JsonFieldCast\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class AbstractMeta implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): \JsonFieldCast\Json\AbstractMeta|\JsonFieldCast\Json\ArrayOfJsonObjectsField
    {
        $data = json_decode($value, true);
        $data = is_array($data) ? $data : [];

        return $this->newInstance(
            $this->getClass($model, $data, $key, $value, $attributes),
            $model,
            $data,
            $key,
            $value,
            $attributes
        );
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value);
    }

    protected function getClass($model, array $data = [], string $key = '', mixed $value = null, array $attributes = []): string
    {
        $class = $this->metaClass();
        if (method_exists($class, 'getCastableClassByModel')) {
            $class = $class::getCastableClassByModel($model, $data);
        }

        return $class;
    }

    protected function newInstance(string $class, $model, array $data = [], string $key = '', mixed $value = null, array $attributes = [])
    {
        return new $class($model, $data);
    }

    abstract protected function metaClass(): string;
}
