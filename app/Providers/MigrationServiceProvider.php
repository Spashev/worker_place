<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Migration services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Migration services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom('vendor/bloomex-common/blca-database/src/migrations');
    }
}
