<?php

namespace Core\Foundation\Template;

trait AudioFields
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
    public static function audio(
        string $id,
        string $placement = '',
        bool $public = true,
        string $path = 'audio',
        string $prefix = 'audio_',
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'audio',
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