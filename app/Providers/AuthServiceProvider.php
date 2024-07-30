<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\SettingsPolicy;
use App\Policies\UserPolicy;
use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        BlcaSetting::class => SettingsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super admin') ? true : null;
        });
    }
}
