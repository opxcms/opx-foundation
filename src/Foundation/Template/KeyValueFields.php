<?php

namespace Core\Foundation\Template;

trait KeyValueFields
{
    /**
     * Make string input record.
     *
     * @param string $id
     * @param string $placement
     * @param array $default
     * @param array $options
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function keyValue(
        string $id,
        string $placement = '',
        array $default = [],
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'key-value',
            $default,
            $info,
            $validation,
            $permissions,
            ['options' => $options]
        );
    }
}