<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            if (!$user || !isset($user->email)) {
                return null;
            }

            $email = strtolower(trim((string) $user->email));
            $superAdmins = config('core.rbac.super_admin_emails', []);

            if (in_array($email, $superAdmins, true)) {
                return true;
            }

            return null;
        });
    }
}
