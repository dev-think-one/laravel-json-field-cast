<?php

namespace JsonFieldCast\Json;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;

trait HasDateAttributes
{
    public function setDateAttribute(string $key, ?\DateTimeInterface $datetime = null, string $format = 'Y-m-d H:i:s'): static
    {
        return $this->setAttribute($key, $datetime?->format($format));
    }

    public function setDate(string $key, ?\DateTimeInterface $datetime = null, string $format = 'Y-m-d H:i:s'): static
    {
        return $this->setDateAttribute($key, $datetime, $format);
    }

    public function setNow(string $key, string $format = 'Y-m-d H:i:s'): static
    {
        return $this->setDateAttribute($key, Carbon::now()->setTimezone(config('app.timezone')), $format);
    }

    public function getDateAttribute(string $key, ?Carbon $default = null): ?Carbon
    {
        $value = $this->getAttribute($key);
        if (is_string($value) && !empty($value)) {
            try {
                return Carbon::parse($value)->setTimezone(config('app.timezone'));
            } catch (InvalidFormatException) {
                // Fail silently and return default
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
            $formats       = Arr::wrap($formats);
            $lastException = null;
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->setTimezone(config('app.timezone'));
                } catch (InvalidFormatException $e) {
                    $lastException = $e;
                }
            }
            if ($lastException instanceof InvalidFormatException) {
                throw $lastException;
            }
        }

        return $default;
    }

    public function getDateTimeFromFormat(string $key, string $format = 'Y-m-d H:i:s', ?Carbon $default = null): ?Carbon
    {
        return $this->getDateTimeFromFormats($key, $format, $default);
    }
}
