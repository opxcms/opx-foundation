<?php

namespace Core\Tools\DB;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Schema\Builder;

class DataBaseCheck
{
    /**
     * Check for tables presence.
     *
     * @param null|string|array $tables
     * @param Builder|null $builder
     *
     * @return  boolean
     * @throws BindingResolutionException
     */
    public static function isTablesMigrated($tables = null, $builder = null): bool
    {
        if (empty($tables)) {
            return true;
        }

        if (is_string($tables)) {
            $tables = [$tables];
        }

        if (!$builder) {
            $builder = app()->make('db')->connection()->getSchemaBuilder();
        }

        $tablesPresent = true;

        foreach ($tables as $table) {
            $tablesPresent = $tablesPresent && self::isTableMigrated($table, $builder);
        }

        return $tablesPresent;
    }

    /**
     * Check if table was migrated.
     *
     * @param string|null $table
     * @param Builder|null $builder
     *
     * @return  boolean
     * @throws BindingResolutionException
     */
    protected static function isTableMigrated(?string $table, $builder = null): bool
    {
        if (!$table) {
            return true;
        }

        if (!$builder) {
            $builder = app()->make('db')->connection()->getSchemaBuilder();
        }

        try {
            return $builder->hasTable($table);
        } catch (Exception $e) {
            return false;
        }
    }
}