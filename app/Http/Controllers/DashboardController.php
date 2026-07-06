<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistik Dashboard
        $stats = [
            'total_risks' => Risk::count(),

            'total_users' => User::count(),

            'high_risks' => Risk::where('level', 'High')->count(),

            'critical_risks' => Risk::where('level', 'Critical')->count(),

            'open_risks' => Risk::where('status', 'Open')->count(),

            'closed_risks' => Risk::where('status', 'Closed')->count(),

            'in_progress_risks' => Risk::where('status', 'In Progress')->count(),
        ];

        // Statistik Risk berdasarkan Category
        $riskCategories = Risk::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category,
                    'total' => $item->total,
                ];
            });

        // Statistik User berdasarkan Role
        $roleStatistics = Role::withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'name' => ucfirst($role->name),
                    'total' => $role->users_count,
                ];
            });

        return view('dashboard', compact(
            'stats',
            'riskCategories',
            'roleStatistics'
        ));
    }
}
