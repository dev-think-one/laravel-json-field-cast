<?php

namespace JsonFieldCast\Tests\Fixtures\Casts\Json;

use Illuminate\Database\Eloquent\Model;
use JsonFieldCast\Json\AbstractMeta;

abstract class AbstractContent extends AbstractMeta
{
    public static function getCastableClassByModel(Model $model, array $data = []): ?string
    {
        $class = ($model->content_type && class_exists($model->content_type))
            ? $model->content_type
            : '';

        throw_if(!$class, new \Exception("Castable class for type [{$model->content_type}] not found"));

        return $class;
    }
}
