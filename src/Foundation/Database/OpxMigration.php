<?php

namespace Core\Foundation\Database;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

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
     * @var  Builder
     */
    protected $schema;

    /**
     * OpxMigration constructor.
     *
     * @return  void
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        Schema::defaultStringLength(191);

        $this->schema = app()->make('db')->connection()->getSchemaBuilder();

        if ($this->blueprint) {
            $this->schema->blueprintResolver(function ($table, $callback) {
                return new $this->blueprint($table, $callback);
            });
        }
    }
}