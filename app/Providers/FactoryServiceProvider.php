<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactoryServiceProvider extends ServiceProvider
{
    /**
     * Factory services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Factory services.
     */
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Bloomex\\Common\\Blca\\Database\\factories\\' . class_basename($modelName) . 'Factory';
        });
    }
}
