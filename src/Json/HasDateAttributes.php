<?php

namespace JsonFieldCast\Json;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait HasDateAttributes
{
    public function getDateAttribute(string $key, ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            try {
                return Carbon::parse($value);
            } catch (InvalidFormatException) {
            }
        }

        return $default;
    }

    /**
     * Trying to get date from one of passed formats.
     *
     * @param string $key
     * @param string|array $formats
     * @param Carbon|null $default
     * @return Carbon|null
     */
    public function getDateTimeFromFormats(string $key, string|array $formats = 'Y-m-d H:i:s', ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            $formats = Arr::wrap($formats);
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (InvalidFormatException $e) {
                }
            }
            if (isset($e) && ($e instanceof InvalidFormatException)) {
                throw $e;
            }
        }

        return $default;
    }

    public function getDateTimeFromFormat(string $key, string $format = 'Y-m-d H:i:s', ?Carbon $default = null): ?Carbon
    {
        return $this->getDateTimeFromFormats($key, $format, $default);
    }
}
