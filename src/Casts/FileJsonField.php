<?php

namespace JsonFieldCast\Casts;

class FileJsonField extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \JsonFieldCast\Json\FileJsonField::class;
    }
}
