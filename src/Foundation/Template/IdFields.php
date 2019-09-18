<?php

namespace Core\Foundation\Template;

trait IdFields
{
    /**
     * Make id input record.
     *
     * @param string $id
     * @param string $placement
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function id(
        string $id,
        string $placement = '',
        string $info = '',
        string $validation = '',
        string $permissions = '|none'
    ): array
    {
        return self::string(
            $id,
            $placement,
            '',
            [],
            $info,
            $validation,
            $permissions
        );
    }

    /**
     * Make parent input record.
     *
     * @param string $id
     * @param string $placement
     * @param array|null $options
     * @param bool $reloadOnChange
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function parent(
        string $id,
        string $placement = '',
        ?array $options = null,
        bool $reloadOnChange = false,
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::nestedSelect(
            $id,
            $placement,
            '',
            $options ?? [],
            true,
            $info,
            $validation,
            $permissions,
            ['needs_reload' => $reloadOnChange]
        );
    }
}