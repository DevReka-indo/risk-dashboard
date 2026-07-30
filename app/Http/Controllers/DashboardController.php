<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\SmapDashboardService;
use App\Services\TopRiskDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected SmapDashboardService $smapDashboardService;
    protected TopRiskDashboardService $topRiskDashboardService;

    public function __construct(
        DashboardService $dashboardService,
        SmapDashboardService $smapDashboardService,
        TopRiskDashboardService $topRiskDashboardService
    ) {
        $this->dashboardService = $dashboardService;
        $this->smapDashboardService = $smapDashboardService;
        $this->topRiskDashboardService = $topRiskDashboardService;
    }

    public function index(Request $request): View
    {
        // 1. Tangkap parameter filter (Default bulan diset ke 0 = Semua Bulan)
        $selectedPeriode = $request->query('periode', 'all');
        $selectedYear    = $request->query('tahun') ? (int) $request->query('tahun') : (int) date('Y');
        $selectedBulan   = $request->has('bulan') ? (int) $request->query('bulan') : 0; // ⬅️ UBAH DISINI: Default 0
        $selectedTab     = $request->query('tab', 'top_risk');

        // 2. Ambil data Dashboard (Departemen)
        $mainDashboardData = $this->dashboardService->getDashboardViewData();

        // 3. Ambil data SMAP
        $smapData = $this->smapDashboardService->getSmapDashboardData($selectedPeriode, $selectedYear);

        // 4. Ambil data Top Risk (Memakai $selectedBulan = 0)
        $topRiskData = $this->getTopRiskDashboardData($selectedBulan, $selectedYear);

        // 5. Merge & kirim ke view
        return view('dashboard.index', array_merge(
            $mainDashboardData,
            $smapData,
            $topRiskData,
            [
                'selectedPeriode' => $selectedPeriode,
                'selectedYear'    => $selectedYear,
                'selectedBulan'   => $selectedBulan,
                'selectedTab'     => $selectedTab,
            ]
        ));
    }

    /**
     * Get Top Risk Dashboard Data
     */
    private function getTopRiskDashboardData(int $bulan, int $tahun): array
    {
        // Ambil data dari service
        $dashboardData = $this->topRiskDashboardService->buildTopRiskDashboardData($bulan, $tahun);

        // Pastikan summary memiliki semua key
        $dashboardData['summary'] = array_merge([
            'total_risiko' => 0,
            'risiko_aktif' => 0,
            'rata_rata_nilai' => 0,
            'tren' => 'Stagnan',
            'total_monitoring' => 0,
        ], $dashboardData['summary'] ?? []);

        // Prepare data untuk Nilai Top Risk
        $nilaiTopRisk = $this->getNilaiTopRisk($bulan, $tahun);

        // Prepare data untuk Unit Level Distribution
        $unitLevelDistribution = $dashboardData['unit_level_distribution'] ?? ['labels' => [], 'datasets' => []];

        // Prepare data untuk Level Distribution Items
        $levelDistributionItems = $this->prepareLevelDistributionItems($dashboardData);

        // Prepare data untuk Trend Risk Items
        $trendRiskItems = $dashboardData['trend_risk'] ?? [];

        // Prepare data untuk Category Distribution Items
        $categoryDistributionItems = $this->prepareCategoryDistributionItems($dashboardData);

        // Prepare data untuk Status Distribution Items
        $statusDistributionItems = $this->prepareStatusDistributionItems($dashboardData);

        // Prepare data untuk Progress Distribution
        $progressDistribution = $dashboardData['progress_distribution'] ?? ['labels' => ['Belum', 'Proses', 'Sudah'], 'data' => [0, 0, 0], 'colors' => ['#FCD34D', '#A3E635', '#93C5FD']];

        // Prepare data untuk Effectiveness Distribution
        $effectivenessDistribution = $dashboardData['effectiveness_distribution'] ?? ['labels' => ['Belum Dinilai'], 'data' => [0], 'colors' => ['#bbf7d0']];

        // Data untuk summary
        $totalRisikoVal = $dashboardData['summary']['total_risiko'] ?? 0;
        $risikoAktifVal = $dashboardData['summary']['risiko_aktif'] ?? 0;
        $avgNilaiVal = $dashboardData['summary']['rata_rata_nilai'] ?? 0;
        $trenVal = $dashboardData['summary']['tren'] ?? 'Stagnan';

        // Data untuk heatmap
        $heatmap = $dashboardData['heatmap'] ?? ['rows' => [], 'risks' => []];

        return [
            'dashboardData' => $dashboardData,
            'nilaiTopRisk' => $nilaiTopRisk,
            'unitLevelDistribution' => $unitLevelDistribution,
            'levelDistributionItems' => $levelDistributionItems,
            'trendRiskItems' => $trendRiskItems,
            'categoryDistributionItems' => $categoryDistributionItems,
            'statusDistributionItems' => $statusDistributionItems,
            'progressDistribution' => $progressDistribution,
            'effectivenessDistribution' => $effectivenessDistribution,
            'totalRisikoVal' => $totalRisikoVal,
            'risikoAktifVal' => $risikoAktifVal,
            'avgNilaiVal' => $avgNilaiVal,
            'trenVal' => $trenVal,
            'heatmap' => $heatmap,
        ];
    }

    /**
     * Get Nilai Top Risk untuk chart
     */
    private function getNilaiTopRisk(int $bulan, int $tahun): array
    {
        $query = \App\Models\TopMonitoringBulanan::with(['risiko', 'level'])
            ->where('tahun', $tahun);

        if ($bulan > 0) {
            $query->where('bulan', $bulan);
        } else {
            // Jika semua bulan (bulan = 0), ambil data monitoring terakhir per id_risiko
            $query->whereIn('id_monitoring', function ($subQuery) use ($tahun) {
                $subQuery->selectRaw('MAX(id_monitoring)')
                    ->from('top_monitoring_bulanan')
                    ->where('tahun', $tahun)
                    ->groupBy('id_risiko');
            });
        }

        $monitorings = $query->get();

        return $monitorings->map(function($monitoring) {
            return [
                'nama_peristiwa_risiko' => $monitoring->risiko?->nama_peristiwa_risiko ?? '-',
                'nilai'                 => (int) ($monitoring->nilai ?? 0),
                'level'                 => $monitoring->level?->nama_level ?? '-',
                'kode_warna'            => $monitoring->level?->kode_warna ?? '#ef4444'
            ];
        })->sortByDesc('nilai')->take(10)->values()->toArray();
    }

    /**
     * Prepare Level Distribution Items
     */
    private function prepareLevelDistributionItems($dashboardData): array
    {
        $items = [];
        $levelColors = [
            'Kritis' => 'bg-rose-500',
            'Tinggi' => 'bg-orange-500',
            'Sedang' => 'bg-amber-400',
            'Rendah' => 'bg-blue-400',
            'Sangat Rendah' => 'bg-emerald-400',
            'High' => 'bg-rose-500',
            'Moderate to High' => 'bg-orange-500',
            'Moderate' => 'bg-amber-400',
            'Low to Moderate' => 'bg-lime-400',
            'Low' => 'bg-emerald-400',
        ];

        $levelData = $dashboardData['level_distribution'] ?? collect([]);

        if ($levelData instanceof \Illuminate\Support\Collection) {
            $levelData = $levelData->toArray();
        }

        foreach ($levelData as $item) {
            $label = $item['label'] ?? 'Tidak Diketahui';
            $items[] = [
                'label' => $label,
                'total' => $item['total'] ?? 0,
                'color' => $item['color'] ?? ($levelColors[$label] ?? 'bg-indigo-600')
            ];
        }

        if (empty($items)) {
            $defaultLevels = ['Kritis', 'Tinggi', 'Sedang', 'Rendah', 'Sangat Rendah'];
            foreach ($defaultLevels as $level) {
                $items[] = [
                    'label' => $level,
                    'total' => 0,
                    'color' => $levelColors[$level] ?? 'bg-indigo-600'
                ];
            }
        }

        return $items;
    }

    /**
     * Prepare Category Distribution Items
     */
    private function prepareCategoryDistributionItems($dashboardData): array
    {
        $items = [];
        $categoryData = $dashboardData['category_distribution'] ?? collect([]);

        if ($categoryData instanceof \Illuminate\Support\Collection) {
            $categoryData = $categoryData->toArray();
        }

        foreach ($categoryData as $item) {
            $items[] = [
                'label' => $item['label'] ?? 'Tidak Diketahui',
                'total' => $item['total'] ?? 0
            ];
        }

        if (empty($items)) {
            $items = [
                ['label' => 'Belum Ada Data', 'total' => 0]
            ];
        }

        return $items;
    }

    /**
     * Prepare Status Distribution Items
     */
    private function prepareStatusDistributionItems($dashboardData): array
    {
        $items = [];
        $statusData = $dashboardData['status_distribution'] ?? collect([]);

        if ($statusData instanceof \Illuminate\Support\Collection) {
            $statusData = $statusData->toArray();
        }

        foreach ($statusData as $item) {
            $items[] = [
                'label' => $item['label'] ?? 'Tidak Diketahui',
                'total' => $item['total'] ?? 0
            ];
        }

        if (empty($items)) {
            $items = [
                ['label' => 'Aktif', 'total' => 0],
                ['label' => 'Tidak Aktif', 'total' => 0],
            ];
        }

        return $items;
    }
}
