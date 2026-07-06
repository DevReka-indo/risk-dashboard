<?php

namespace App\Providers;

use App\Models\VdptMonitoring;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }

            return null;
        });

        Route::model('risk', VdptMonitoring::class);
    }
}
