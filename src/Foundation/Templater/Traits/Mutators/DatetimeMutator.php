<?php

namespace Core\Foundation\Templater\Traits\Mutators;

use Carbon\Carbon;

class DatetimeMutator implements MutatorInterface
{
    /**
     * Transform Carbon date to ISO format.
     *
     * @param mixed $value
     *
     * @return  string|null
     */
    public static function get($value): ?string
    {
        if ($value instanceof Carbon) {
            $value = $value->toIso8601String();
        }

        return $value;
    }

    /**
     * Transform ISO format to Carbon object.
     *
     * @param mixed $value
     * @param array $field
     *
     * @return  Carbon|null
     */
    public static function set($value, $field): ?Carbon
    {
        if (is_string($value) && !empty($value)) {
            $value = Carbon::parse($value)->setTimezone(config('app.timezone'));
        }

        return $value;
    }
}