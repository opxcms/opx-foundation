<?php

namespace Core\Foundation\ListHelpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Filters
{
    /**
     * Make show deleted filters.
     *
     * @param array $filters
     * @param bool $enabled
     * @param bool $onlyDeleted
     *
     * @return  array
     */
    public static function addDeletedFilter($filters = [], $enabled = false, $onlyDeleted = false): array
    {
        $filters['show_deleted'] = [
            'caption' => 'filters.filter_by_deleted',
            'type' => 'checkbox',
            'enabled' => $enabled,
            'value' => $onlyDeleted ? 'only_deleted' : 'show_deleted',
            'options' => ['show_deleted' => 'filters.filter_value_deleted', 'only_deleted' => 'filters.filter_value_only_deleted'],
        ];

        return $filters;
    }

    /**
     * Process deleted filter.
     *
     * @param EloquentBuilder $query
     * @param array|null $filters
     *
     * @return  EloquentBuilder
     */
    public static function processDeletedFilter(EloquentBuilder $query, $filters = null): EloquentBuilder
    {
        if ($filters === null || !isset($filters['show_deleted'])) {
            return $query;
        }

        if ($filters['show_deleted'] === 'show_deleted') {
            $query->withTrashed();
        } elseif ($filters['show_deleted'] === 'only_deleted') {
            $query->onlyTrashed();
        }

        return $query;
    }

    /**
     * Add enabled filters
     *
     * @param array $filters
     * @param bool $enabled
     * @param bool $showEnabled
     *
     * @return  array
     */
    public static function addEnabledFilter($filters = [], $enabled = false, $showEnabled = true): array
    {
        $filters['enabled'] = [
            'caption' => 'filters.filter_by_enabled',
            'type' => 'checkbox',
            'enabled' => $enabled,
            'value' => $showEnabled ? 'enabled' : 'disabled',
            'options' => ['enabled' => 'filters.filter_value_enabled', 'disabled' => 'filters.filter_value_disabled'],
        ];

        return $filters;
    }

    /**
     * Process enabled filter.
     *
     * @param EloquentBuilder $query
     * @param null $filters
     * @param string $column
     *
     * @return  EloquentBuilder
     */
    public static function processEnabledFilter(EloquentBuilder $query, $filters = null, $column = 'enabled'): EloquentBuilder
    {
        if ($filters === null || !isset($filters['enabled'])) {
            return $query;
        }

        if ($filters['enabled'] === 'enabled') {
            $query->where($column, '=', true);
        } elseif ($filters['enabled'] === 'disabled') {
            $query->where($column, '=', false);
        }

        return $query;
    }

    /**
     * Add hidden filter.
     *
     * @param array $filters
     * @param bool $enabled
     * @param bool $showHidden
     *
     * @return  array
     */
    public static function addHiddenFilter($filters = [], $enabled = false, $showHidden = false): array
    {
        $filters['hidden'] = [
            'caption' => 'filters.filter_by_hidden',
            'type' => 'checkbox',
            'enabled' => $enabled,
            'value' => $showHidden ? 'hidden' : 'shown',
            'options' => ['hidden' => 'filters.filter_value_hidden', 'shown' => 'filters.filter_value_shown'],
        ];

        return $filters;
    }

    /**
     * Process hidden filter.
     *
     * @param EloquentBuilder $query
     * @param null $filters
     * @param string $column
     *
     * @return  EloquentBuilder
     */
    public static function processHiddenFilter(EloquentBuilder $query, $filters = null, $column = 'hidden'): EloquentBuilder
    {
        if ($filters === null || !isset($filters['enabled'])) {
            return $query;
        }

        if ($filters['hidden'] === 'hidden') {
            $query->where($column, '=', true);
        } elseif ($filters['hidden'] === 'shown') {
            $query->where($column, '=', false);
        }

        return $query;
    }

    /**
     * Add published filter.
     *
     * @param array $filters
     * @param bool $enabled
     * @param bool $showHidden
     *
     * @return  array
     */
    public static function addPublishedFilter($filters = [], $enabled = false, $showHidden = false): array
    {
        $filters['published'] = [
            'caption' => 'filters.filter_by_published',
            'type' => 'checkbox',
            'enabled' => false,
            'value' => 'published',
            'options' => ['published' => 'filters.filter_value_published', 'unpublished' => 'filters.filter_value_unpublished'],
        ];

        return $filters;
    }

    /**
     * Process published filter.
     *
     * @param EloquentBuilder $query
     * @param null $filters
     * @param string $publishedField
     * @param string $publishStartField
     * @param string $publishEndField
     *
     * @return  EloquentBuilder
     */
    public static function processPublishedFilter(EloquentBuilder $query, $filters = null, $publishedField = 'published', $publishStartField = 'publish_start', $publishEndField = 'publish_end'): EloquentBuilder
    {
        if ($filters === null || !isset($filters['published'])) {
            return $query;
        }

        $now = Carbon::now()->toDateTimeString();
        $show = $filters['published'] === 'published' ? 0 : 1;
        $query->whereRaw("IF({$publishedField} = 1 AND (ISNULL({$publishStartField}) OR STR_TO_DATE('{$now}', '%Y-%m-%d %H:%i:%s') > {$publishStartField}) AND (ISNULL({$publishEndField}) OR STR_TO_DATE('{$now}', '%Y-%m-%d %H:%i:%s') < {$publishEndField}), 0, 1) = {$show}");

        return $query;
    }

}