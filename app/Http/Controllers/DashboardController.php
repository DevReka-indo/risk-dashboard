<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DepMonitoring;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Mencari id_level untuk tingkat "High" atau "Tinggi" berdasarkan nama_level asli Anda
        $highLevelId = LevelRisiko::where('nama_level', 'High')
            ->orWhere('nama_level', 'Tinggi')
            ->value('id_level');

        $stats = [
            'total_risks' => DepMonitoring::count(),

            'total_users' => User::count(),

            'high_risks' => DepMonitoring::where('id_level', $highLevelId)->count(),

            'active_risks' => DepMonitoring::where('status', true)->count(),

            'inactive_risks' => DepMonitoring::where('status', false)->count(),
        ];

        // Risk by Category — Menggunakan model KategoriRisiko dan relasi depMonitorings
        $riskCategories = KategoriRisiko::withCount('depMonitorings')
            ->having('dep_monitorings_count', '>', 0)
            ->orderByDesc('dep_monitorings_count')
            ->get()
            ->map(fn (KategoriRisiko $cat) => [
                'name' => $cat->nama_kategori,
                'total' => $cat->dep_monitorings_count,
            ]);

        // User by Role
        $roleStatistics = Role::withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'name' => ucfirst($role->name),
                'total' => $role->users_count,
            ]);

        return view('dashboard', compact(
            'stats',
            'riskCategories',
            'roleStatistics',
            'highLevelId',
        ));
    }
}
