<?php


namespace JsonFieldCast\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class AbstractMeta implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): \JsonFieldCast\Json\AbstractMeta
    {
        $data  = json_decode($value, true);
        $class = $this->metaClass();

        return new $class($model, is_array($data) ? $data : []);
    }

    public function set($model, $key, $value, $attributes): string
    {
        return json_encode($value);
    }

    abstract protected function metaClass(): string;
}
