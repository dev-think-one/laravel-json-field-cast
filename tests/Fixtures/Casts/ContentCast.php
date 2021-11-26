<?php

namespace JsonFieldCast\Tests\Fixtures\Casts;

use JsonFieldCast\Casts\AbstractMeta;
use JsonFieldCast\Tests\Fixtures\Casts\Json\AbstractContent;

class ContentCast extends AbstractMeta
{
    protected function metaClass(): string
    {
        return AbstractContent::class;
    }
}
