<?php

namespace Core\Foundation\Module\Traits;

use Core\Tools\DB\DataBaseCheck;

trait CheckMigrations
{
    /**
     * Check for tables listed in config are migrated.
     *
     * @return boolean
     */
	public function isMigrated(): bool
    {
		if(empty($this->config('tables'))) {
			return true;
		}

		return DataBaseCheck::isTablesMigrated(
		    $this->config('tables'),
            $this->app->make('db')->connection()->getSchemaBuilder()
        );
	}
}