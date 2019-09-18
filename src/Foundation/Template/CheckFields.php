<?php

namespace Core\Foundation\Template;

use Illuminate\Database\Eloquent\Collection;

trait CheckFields
{
    /**
     * Make checkbox input record.
     *
     * @param string $id
     * @param string $placement
     * @param bool $default
     * @param string $info
     * @param string $validation
     * @param string $permissions
     *
     * @return  array
     */
    public static function checkbox(
        string $id,
        string $placement = '',
        bool $default = false,
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField($id, $placement, 'checkbox', $default, $info, $validation, $permissions);
    }

    /**
     * Make checkbox list input record.
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
    public static function checkboxList(
        string $id,
        string $placement = '',
        array $default = [],
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField($id, $placement, 'checkbox-list', $default, $info, $validation, $permissions, ['options' => $options]);
    }

    /**
     * Make checkbox grouped list input record.
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
    public static function checkboxGroupedList(
        string $id,
        string $placement = '',
        array $default = [],
        array $options = [],
        string $info = '',
        string $validation = '',
        string $permissions = ''
    ): array
    {
        return self::makeField($id, $placement, 'checkbox-grouped-list', $default, $info, $validation, $permissions, ['options' => $options]);
    }

    /**
     * Make data for checkbox or radio list.
     *
     * @param string $modelClass
     * @param string $captionField
     * @param string $idField
     *
     * @return  array
     */
    public static function makeCheckList(string $modelClass, string $captionField = 'name', string $idField = 'id'): array
    {
        /** @var Collection $models */
        $models = call_user_func([$modelClass, 'all'], ["{$idField} as id", "{$captionField} as caption"]);

        return $models->toArray();
    }

    /**
     * Make data for grouped checkbox list.
     *
     * @param string $groupClass
     * @param string $relationName
     * @param string $groupCaptionField
     * @param string $captionField
     * @param string $idField
     *
     * @return  array
     */
    public static function makeCheckGroupedList(string $groupClass, string $relationName, string $groupCaptionField = 'name', string $captionField = 'name', string $idField = 'id'): array
    {
        /** @var Collection $models */
        $models = call_user_func([$groupClass, 'all']);
        $models->load($relationName);

        $result = [];

        $models->map(static function ($model) use (&$result, $relationName, $groupCaptionField, $captionField, $idField){
            if($model->{$relationName}->count() > 0) {
                $group = ['children' => []];
                $group['caption'] = $model->{$groupCaptionField};
                foreach ($model->{$relationName} as $relation) {
                    $group['children'][] = ['id' => $relation->{$idField}, 'caption' => $relation->{$captionField}];
                }
                $result[] = $group;
            }
        });

        return $result;
    }
}