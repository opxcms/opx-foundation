<?php

namespace Core\Foundation\Template;

trait Comments
{
    /**
     * Make comments field.
     *
     * @param string $id
     * @param string $placement
     * @param string $info
     * @param string $orderBy
     * @param string $orderDirection
     * @param bool $showCommentator
     * @param string $permissions
     *
     * @return  array
     */
    public static function comments(
        string $id,
        string $placement = '',
        string $info = '',
        string $orderBy = 'date',
        string $orderDirection = 'asc',
        bool $showCommentator = true,
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'comments',
            null,
            $info,
            '',
            $permissions,
            [
                'order_by' => $orderBy,
                'order_direction' => $orderDirection,
                'show_commentator' => $showCommentator,
            ]
        );
    }
}