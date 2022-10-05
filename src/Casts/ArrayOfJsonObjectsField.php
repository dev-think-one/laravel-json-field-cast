<?php

namespace JsonFieldCast\Casts;

class ArrayOfJsonObjectsField extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \JsonFieldCast\Json\ArrayOfJsonObjectsField::class;
    }
}
