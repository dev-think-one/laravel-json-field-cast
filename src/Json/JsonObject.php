<?php

namespace JsonFieldCast\Json;

class JsonObject implements \JsonSerializable
{
    use HasDataArrayWithAttributes;

    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}
