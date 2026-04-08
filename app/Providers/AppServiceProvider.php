<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Admins pass ALL permission/gate checks automatically
        Gate::before(function ($user, $ability) {
            try {
                if ($user->hasRole('admin')) {
                    return true;
                }
            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
