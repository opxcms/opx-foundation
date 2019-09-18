<?php

namespace Core\Foundation\ListHelpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Orders
{
    /**
     * Make order record.
     *
     * @param string $defaultField
     * @param string $defaultDirection
     *
     * @return  array
     */
    public static function makeOrder($defaultField = 'id', $defaultDirection = 'asc'): array
    {
        return [
            'current' => $defaultField,
            'direction' => $defaultDirection,
            'fields' => [],
        ];
    }

    /**
     * Add order field.
     *
     * @param array $order
     * @param string $name
     * @param string|null $caption
     *
     * @return  array
     */
    public static function makeOrderField(array $order, string $name, ?string $caption = null): array
    {
        if ($caption === null) {
            $caption = "orders.sort_by_{$name}";
        }

        $order['fields'][$name] = $caption;

        return $order;
    }

    /**
     * Apply orders to
     *
     * @param array|null $order
     * @param $default $direction
     *
     * @return  string
     */
    public static function getDirection(?array $order, $default = 'asc'): string
    {
        $direction = strtolower($order['direction'] ?? $default);

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = $default;
        }

        return $direction;
    }

    /**
     * Process simple order.
     *
     * @param EloquentBuilder $query
     * @param string $column
     * @param string $direction
     *
     * @return  EloquentBuilder
     */
    public static function processSimpleOrder(EloquentBuilder $query, $column, $direction): EloquentBuilder
    {
        $query->orderBy($column, $direction);

        return $query;
    }

    /**
     * Process date order, move null-dates last.
     *
     * @param EloquentBuilder $query
     * @param string $column
     * @param string $direction
     *
     * @return  EloquentBuilder
     */
    public static function processDateOrder(EloquentBuilder $query, $column, $direction): EloquentBuilder
    {
        $query->orderByRaw("ISNULL({$column}) asc")->orderBy($column, $direction);

        return $query;
    }

    /**
     * Process published order.
     *
     * @param EloquentBuilder $query
     * @param string $publishedField
     * @param string $publishStartField
     * @param string $publishEndField
     * @param string $direction
     *
     * @return  EloquentBuilder
     */
    public static function processPublishedOrder(EloquentBuilder $query, $direction, $publishedField = 'published', $publishStartField = 'publish_start', $publishEndField = 'publish_end'): EloquentBuilder
    {
        $now = Carbon::now()->toDateTimeString();

        $query->orderByRaw("IF({$publishedField}} = 1 AND (ISNULL({$publishStartField}) OR STR_TO_DATE('{$now}', '%Y-%m-%d %H:%i:%s') > {$publishStartField}) AND (ISNULL({$publishEndField}) OR STR_TO_DATE('{$now}', '%Y-%m-%d %H:%i:%s') < {$publishEndField}), 0, 1) {$direction}");

        return $query;
    }
}