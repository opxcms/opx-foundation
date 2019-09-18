<?php

namespace Core\Foundation\Tools\DB;


class DataBaseCheck
{
    /**
     * Check for tables presence.
     *
     * @param  null|string|array  $tables
     * @param  \Illuminate\Database\Schema\Builder|null  $builder
     *
     * @return  boolean
     */
    public static function isTablesMigrated($tables = null, $builder = null)
    {
        if(empty($tables)) {
            return true;
        }

        if(is_string($tables)) {
            $tables = [$tables];
        }

        if(! $builder) {
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
     * @param  string|null  $table
     * @param  \Illuminate\Database\Schema\Builder|null  $builder
     *
     * @return  boolean
     */
    protected static function isTableMigrated($table, $builder = null)
    {
        if(! $table) {
            return true;
        }

        if(! $builder) {
            $builder = app()->make('db')->connection()->getSchemaBuilder();
        }

        return $builder->hasTable($table);
    }
}