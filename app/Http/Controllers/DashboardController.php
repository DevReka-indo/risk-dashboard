<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\SmapDashboardService; // ⬅️ Gunakan SmapDashboardService
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected SmapDashboardService $smapDashboardService;

    public function __construct(
        DashboardService $dashboardService,
        SmapDashboardService $smapDashboardService
    ) {
        $this->dashboardService = $dashboardService;
        $this->smapDashboardService = $smapDashboardService;
    }

    public function index(Request $request): View
    {
        // 1. Tangkap parameter filter
        $selectedPeriode = $request->query('periode', 'all');
        $selectedYear    = $request->query('tahun') ? (int) $request->query('tahun') : (int) date('Y');

        // 2. Ambil data
        $mainDashboardData = $this->dashboardService->getDashboardViewData();
        $smapData          = $this->smapDashboardService->getSmapDashboardData($selectedPeriode, $selectedYear);

        // 3. Merge & kirim ke view
        return view('dashboard.index', array_merge(
            $mainDashboardData,
            $smapData,
            [
                'selectedPeriode' => $selectedPeriode,
                'selectedYear'    => $selectedYear,
            ]
        ));
    }
}
