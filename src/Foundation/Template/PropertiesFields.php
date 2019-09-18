<?php

namespace Core\Foundation\Template;

use Illuminate\Database\Eloquent\Collection;

trait PropertiesFields
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
    public static function properties(
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
            'properties',
            $default,
            $info,
            $validation,
            $permissions,
            ['options' => $options]
        );
    }

    /**
     * Make list of properties.
     *
     * @param string $modelClass
     * @param string $idField
     * @param string $aliasField
     * @param string $nameField
     * @param string $unitsField
     * @param string $typeField
     * @param string $valuesField
     *
     * @return  array
     */
    public static function makePropertyList(
        string $modelClass,
        string $idField = 'id',
        string $aliasField = 'alias',
        string $nameField = 'name',
        string $unitsField = 'units',
        string $typeField = 'type',
        string $valuesField = 'values'
    ): array
    {
        $parameters = [
            "{$idField} as id",
            "{$aliasField} as alias",
            "{$nameField} as name",
            "{$unitsField} as units",
            "{$typeField} as type",
            "{$valuesField} as values",
        ];

        /** @var Collection $models */
        $models = call_user_func([$modelClass, 'all'], $parameters);
        $models = $models->sortBy('name');

        return array_values($models->toArray());
    }
}