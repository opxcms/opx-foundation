<?php

namespace Core\Foundation\Template;

use Carbon\Carbon;

trait DateTimeFields
{
    /**
     * Make datetime input record.
     *
     * @param string $id
     * @param string $placement
     * @param string|Carbon|null $default
     * @param string $info
     * @param string $validation
     * @param string $permissions
     * @param int $minuteStep
     *
     * @return  array
     */
    public static function datetime(
        string $id,
        string $placement = '',
        $default = null,
        string $info = '',
        string $validation = '',
        string $permissions = '',
        int $minuteStep = 5
    ): array
    {
        if ($default === null) {
            $default = '';
        } elseif
        (is_string($default)) {
            $default = Carbon::parse($default);
        }

        return self::makeField($id, $placement, 'datetime', $default, $info, $validation, $permissions, ['minute_step' => $minuteStep]);
    }
}