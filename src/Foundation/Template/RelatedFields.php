<?php

namespace Core\Foundation\Template;

trait RelatedFields
{
    /**
     * Make related list input records.
     *
     * @param string $id
     * @param string $placement
     * @param string $info
     * @param string $listLoaderUrl
     * @param string $fetchDetailsUrl
     * @param string $valueType
     * @param bool $safeId
     * @param bool $canAdd
     * @param bool $canEdit
     * @param bool $canRemove
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function related(
        string $id,
        string $placement = '',
        string $info = '',
        string $listLoaderUrl = '',
        string $fetchDetailsUrl = '',
        string $valueType = 'none',
        bool $safeId = true,
        bool $canAdd = true,
        bool $canEdit = true,
        bool $canRemove = true,
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField(
            $id,
            $placement,
            'related',
            null,
            $info,
            $validation,
            $permissions,
            [
                'list_loader_url' => $listLoaderUrl,
                'fetch_details_url' => $fetchDetailsUrl,
                'value_type' => $valueType,
                'can_add_related' => $canAdd,
                'can_edit_related' => $canEdit,
                'can_remove_related' => $canRemove,
                'safe_id' => $safeId,
            ]
        );
    }
}