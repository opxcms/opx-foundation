<?php

namespace Core\Foundation\Template;

trait ImageFields
{
    /**
     * Make image input records.
     *
     * @param string $id
     * @param string $placement
     * @param bool $public
     * @param string $path
     * @param string $prefix
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function image(
        string $id,
        string $placement = '',
        bool $public = true,
        string $path = 'images',
        string $prefix = 'img_',
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'image',
            null,
            $info,
            $validation,
            $permissions,
            [
                'public' => $public,
                'path' => $path,
                'prefix' => $prefix,
            ]
        );
    }
}