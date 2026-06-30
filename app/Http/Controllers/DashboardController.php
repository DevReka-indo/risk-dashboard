<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_risks' => 128,
            'internal_risks' => 86,
            'external_risks' => 42,
            'high_risks' => 18,
            'extreme_risks' => 7,
            'overdue_mitigations' => 12,
        ];

        $riskCategories = [
            ['name' => 'Strategic', 'total' => 24],
            ['name' => 'Operational', 'total' => 38],
            ['name' => 'Financial', 'total' => 17],
            ['name' => 'Compliance', 'total' => 21],
            ['name' => 'IT / Cyber', 'total' => 15],
            ['name' => 'External', 'total' => 13],
        ];

        return view('dashboard', compact('stats', 'riskCategories'));
    }
}
