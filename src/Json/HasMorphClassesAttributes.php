<?php

namespace JsonFieldCast\Json;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasMorphClassesAttributes
{
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
}
