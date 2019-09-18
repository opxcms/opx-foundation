<?php

namespace Core\Foundation\Database;

use Illuminate\Database\Migrations\Migration;

abstract class OpxMigration extends Migration
{
    /**
     * Blueprint class to automatic resolve.
     *
     * @var  string
     */
    protected $blueprint = OpxBlueprint::class;

    /**
     * Schema builder.
     *
     * @var  \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * OpxMigration constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->schema = app()->make('db')->connection()->getSchemaBuilder();

        if ($this->blueprint) {
            $this->schema->blueprintResolver(function ($table, $callback) {
                return new $this->blueprint($table, $callback);
            });
        }
    }
}