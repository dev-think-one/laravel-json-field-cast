<?php


namespace JsonFieldCast\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class AbstractMeta implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): \JsonFieldCast\Json\AbstractMeta| \JsonFieldCast\Json\ArrayOfJsonObjectsField
    {
        $data  = json_decode($value, true);
        $data  = is_array($data) ? $data : [];
        $class = $this->metaClass();

        if (method_exists($class, 'getCastableClassByModel')) {
            $class = $class::getCastableClassByModel($model, $data);
        }

        return new $class($model, $data);
    }

    public function set($model, $key, $value, $attributes): string
    {
        return json_encode($value);
    }

    abstract protected function metaClass(): string;
}
