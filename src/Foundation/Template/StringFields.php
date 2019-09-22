<?php

namespace Core\Foundation\Template;

trait StringFields
{
    /**
     * Make string input record.
     *
     * @param string $id
     * @param string $placement
     * @param string $default
     * @param array $options
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function string(
        string $id,
        string $placement = '',
        string $default = '',
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'string',
            $default,
            $info,
            $validation,
            $permissions,
            ['options' => $options]
        );
    }

    /**
     * Make phone input record.
     *
     * @param string $id
     * @param string $placement
     * @param string $default
     * @param string $info
     * @param string $format
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function phone(
        string $id,
        string $placement = '',
        string $default = '',
        string $info = '',
        string $format = '\+\7 (111) 111-11-11',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField($id, $placement, 'mask', $default, $info, $validation, $permissions, [
            'mask' => $format,
            'input-type' => 'tel',
        ]);
    }

    /**
     * Make text input record.
     *
     * @param string $id
     * @param string $placement
     * @param string $default
     * @param array $options
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function text(
        string $id,
        string $placement = '',
        string $default = '',
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'text',
            $default,
            $info,
            $validation,
            $permissions,
            ['options' => $options]
        );
    }

    /**
     * Make html input record.
     *
     * @param string $id
     * @param string $placement
     * @param string $default
     * @param array $options
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function html(
        string $id,
        string $placement = '',
        string $default = '',
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'html',
            $default,
            $info,
            $validation,
            $permissions,
            ['options' => $options]
        );
    }

    /**
     * Make string input record.
     *
     * @param string $id
     * @param string $placement
     * @param array $options
     * @param string $info
     * @param string $permissions
     *
     * @return  array
     */
    public static function link(
        string $id,
        string $placement = '',
        array $options = [],
        string $info = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'link',
            null,
            $info,
            $permissions,
            $permissions,
            ['options' => $options]
        );
    }
}