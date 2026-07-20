<?php

namespace App\Providers;

use App\Models\VdptMonitoring;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

// Import semua model
use App\Models\SmapMonitoring;
use App\Models\TopRisiko;
use App\Models\DepMonitoring;

// Import semua observer
use App\Observers\SmapMonitoringObserver;
use App\Observers\TopRisikoObserver;
use App\Observers\DepartemenRiskObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Logika bawaan Anda (jangan dihapus)
        Gate::before(function ($user, string $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }

            return null;
        });

        Route::model('risk', VdptMonitoring::class);

        // --- TAMBAHKAN PENDAFTARAN OBSERVER DI BAWAH INI ---
        SmapMonitoring::observe(SmapMonitoringObserver::class);
        TopRisiko::observe(TopRisikoObserver::class);
        DepMonitoring::observe(DepartemenRiskObserver::class);
    }
}
