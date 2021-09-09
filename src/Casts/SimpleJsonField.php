<?php

namespace JsonFieldCast\Casts;

class SimpleJsonField extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \JsonFieldCast\Json\SimpleJsonField::class;
    }
}
