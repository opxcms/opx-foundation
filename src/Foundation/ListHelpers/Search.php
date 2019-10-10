<?php

namespace Core\Foundation\ListHelpers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class Search
{
    /**
     * Add search option.
     *
     * @param array $search
     * @param string $name
     * @param bool $default
     * @param string|null $caption
     *
     * @return  array
     */
    public static function addSearchField(array $search, string $name, bool $default = true, ?string $caption = null): array
    {
        if ($caption === null) {
            $caption = "search.search_by_{$name}";
        }

        $search[$name] = [
            'caption' => $caption,
            'default' => $default,
        ];

        return $search;
    }

    /**
     * Apply search conditions to query.
     *
     * @param EloquentBuilder $query
     * @param $search
     * @param array $columnNames
     *
     * @return  EloquentBuilder
     */
    public static function applySearch(EloquentBuilder $query, $search, array $columnNames): EloquentBuilder
    {
        if (empty($search['subject']) || empty($search['fields'])) {
            return $query;
        }

        $subject = str_replace('*', '%', $search['subject']);
        $fields = explode(',', $search['fields']);

        $query = $query->where(static function ($q) use ($fields, $subject, $columnNames) {
            /** @var Builder $q */

            foreach ($columnNames as $name => $column) {
                if (!is_string($name)) {
                    $name = $column;
                }

                if (in_array($name, $fields, true)) {
                    $q->orWhere($column, 'LIKE', $subject);
                }
            }
        });

        return $query;
    }
}