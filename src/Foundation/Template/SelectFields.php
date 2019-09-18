<?php

namespace Core\Foundation\Template;

use Illuminate\Database\Eloquent\Collection;

trait SelectFields
{
    /**
     * Make select input record.
     *
     * @param string $id
     * @param string $placement
     * @param string|null $default
     * @param array $options
     * @param bool $withNumericKeys
     * @param string $info
     * @param string $validation
     * @param string $permissions
     * @param array $fields
     *
     * @return  array
     */
    public static function select(
        string $id,
        string $placement = '',
        ?string $default = '',
        array $options = [],
        bool $withNumericKeys = false,
        string $info = '',
        string $validation = '',
        string $permissions = '',
        array $fields = []
    ): array
    {
        $transformed = [];

        foreach ($options as $key => $option) {
            if (!$withNumericKeys && !is_string($key) && !is_array($option)) {
                $key = $option;
            }

            $transformed[$key] = $option;
        }
        return self::makeField($id, $placement, 'select', $default, $info, $validation, $permissions, array_merge(['options' => $transformed], $fields));
    }

    /**
     * Make select input record.
     *
     * @param string $id
     * @param string $placement
     * @param string|null $default
     * @param array $options
     * @param bool $safeId
     * @param string $info
     * @param string $validation
     * @param string $permissions
     * @param array $fields
     *
     * @return  array
     */
    public static function nestedSelect(
        string $id,
        string $placement = '',
        ?string $default = '',
        array $options = [],
        bool $safeId = false,
        string $info = '',
        string $validation = '',
        string $permissions = '',
        array $fields = []
    ): array
    {
        return self::makeField($id, $placement, 'nested-select', $default, $info, $validation, $permissions, array_merge(['options' => $options, 'safe_id' => $safeId], $fields));
    }

    /**
     * Make data for nested list.
     *
     * @param string $modelClass
     * @param bool $withTrashed
     * @param string $captionField
     * @param string $idField
     *
     * @return  array
     */
    public static function makeList(string $modelClass, bool $withTrashed = false, string $captionField = 'name', string $idField = 'id'): array
    {
        /** @var Collection $models */
        if ($withTrashed === true) {
            $modelClass = call_user_func([$modelClass, 'withTrashed']);
            $models = call_user_func([$modelClass, 'get'], ["{$idField} as id", "{$captionField} as caption"]);
        } else {
            $models = call_user_func([$modelClass, 'all'], ["{$idField} as id", "{$captionField} as caption"]);
        }


        return $models->toArray();
    }

    /**
     * Make data for nested list.
     *
     * @param string $modelClass
     * @param string $captionField
     * @param string $idField
     * @param string $parentIdField
     *
     * @return  array
     */
    public static function makeNestedList(string $modelClass, string $captionField = 'name', string $idField = 'id', string $parentIdField = 'parent_id'): array
    {
        /** @var Collection $models */
        $models = call_user_func([$modelClass, 'all'], ["{$idField} as id", "{$parentIdField} as parent_id", "{$captionField} as caption"]);

        return $models->toArray();
    }
}