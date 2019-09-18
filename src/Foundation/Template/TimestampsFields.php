<?php

namespace Core\Foundation\Template;

trait TimestampsFields
{
    public static function timestampCreatedAt(string $placement = 'general/timestamps'): array
    {
        return self::datetime('created_at', $placement, null, '', '', '|none');
    }

    public static function timestampUpdatedAt(string $placement = 'general/timestamps'): array
    {
        return self::datetime('updated_at', $placement, null, '', '', '|none');
    }

    public static function timestampDeletedAt(string $placement = 'general/timestamps'): array
    {
        return self::datetime('deleted_at', $placement, null, '', '', '|none');
    }
}