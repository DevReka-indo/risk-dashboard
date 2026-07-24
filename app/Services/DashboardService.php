<?php

namespace App\Services;

use App\Models\DepMonitoring;
use App\Models\LevelRisiko;
use App\Models\SmapMonitoring;
use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    protected DashboardRepository $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardViewData(): array
    {
        $highLevelIds = $this->repository->getHighLevelIds();

        $stats = $this->repository->getKpiStats($highLevelIds);
        $levelDistribution = $this->repository->getLevelDistribution();

        $riskCategories = $this->repository->getTopRiskCategories(6)->map(fn ($cat) => [
            'name' => $cat->nama_kategori,
            'total' => $cat->dep_monitorings_count,
        ]);

        $topHighRisks = $this->repository->getTopHighRisks($highLevelIds, 5);
        $recentUpdates = $this->repository->getRecentUpdates(5);

        $heatmap = $this->getHeatmapData();
        $smapData = $this->getSmapData();
        $departemenData = $this->getDepartemenChartData();

        // Extracting SMAP Data dengan Fallback Array Kosong (bukan null)
        $smapPieData = $smapData['pieData'] ?? ['labels' => [], 'inherent' => [], 'current' => [], 'target' => []];
        $smapRisks = $smapData['risks'] ?? collect([]);
        $smapUnitTable = $smapData['unitTable'] ?? collect([]);
        $smapKomposisiData = $smapData['komposisi'] ?? ['labels' => [], 'datasets' => []];

        $selectedPeriode = (int) request('periode', 1);
        $selectedYear = (int) request('tahun', date('Y'));

        return [
            'stats' => $stats,
            'levelDistribution' => $levelDistribution,
            'riskCategories' => $riskCategories,
            'topHighRisks' => $topHighRisks,
            'recentUpdates' => $recentUpdates,
            'heatmap' => $heatmap,

            // Data SMAP Khusus
            'smapData' => $smapData,
            'smapPieData' => $smapPieData,
            'smapRisks' => $smapRisks,
            'smapUnitTable' => $smapUnitTable,
            'smapKomposisiData' => $smapKomposisiData,

            // Filter Periode
            'selectedPeriode' => $selectedPeriode,
            'selectedYear' => $selectedYear,
            'periodText' => "Triwulan {$selectedPeriode} - {$selectedYear}",

            // Summary Statistik
            'summary' => [
                'total_risiko' => $stats['total_smap'] ?? 0,
                'high_risk' => $stats['high_risks'] ?? 0,
                'medium_risk' => $levelDistribution['Moderate'] ?? 0,
                'low_risk' => $levelDistribution['Low'] ?? 0,
            ],

            // Dummy/Default Fallback Chart
            'dashboardData' => [],
            'catLabels' => ['Operasional', 'Kepatuhan', 'Finansial', 'Strategis'],
            'catData' => [10, 5, 8, 3],
            'trendLabels' => ['TW I', 'TW II', 'TW III', 'TW IV'],
            'trendData' => [12, 18, 15, 22],

            // Data Departemen (Menggunakan Prefix Khusus agar Tidak Bentrok)
            'departemenData' => $departemenData,
            'deptLabels' => $departemenData['labels'] ?? [],
            'deptData' => $departemenData['data'] ?? [],
            'inherentData' => $departemenData['inherentData'] ?? [],
            'currentData' => $departemenData['currentData'] ?? [],
            'targetData' => $departemenData['targetData'] ?? [],
            'levelLabels' => $departemenData['levelLabels'] ?? [],
            'jenisRisikoData' => $departemenData['jenisRisikoData'] ?? [],
            'efektifRisikoData' => $departemenData['efektifRisikoData'] ?? [],
            'progresData' => $departemenData['progresData'] ?? [],
            'progresPerUnit' => $departemenData['progresPerUnit'] ?? [],
            'smapChartDatasets' => $smapKomposisiData['datasets'] ?? [],
        ];
    }

    public function getHeatmapData(): array
    {
        $risks = $this->repository->getRisksForHeatmap();

        if ($risks->isEmpty()) {
            return $this->getDummyHeatmapData();
        }

        $matrix = [];
        for ($i = 0; $i < 5; $i++) {
            $row = [];
            for ($j = 0; $j < 5; $j++) {
                $row[] = [
                    'value' => 0,
                    'class' => $this->getHeatmapCellClass(0),
                    'risks' => [],
                ];
            }
            $matrix[] = $row;
        }

        foreach ($risks as $risk) {
            $value = $risk->value;
            if ($value >= 1 && $value <= 25) {
                $impact = ceil($value / 5);
                $likelihood = ceil($value / $impact);

                $impact = min($impact, 5);
                $likelihood = min($likelihood, 5);

                $rowIndex = $impact - 1;
                $colIndex = $likelihood - 1;

                $matrix[$rowIndex][$colIndex]['value'] = $value;
                $matrix[$rowIndex][$colIndex]['risks'][] = [
                    'code' => 'R'.str_pad($risk->id_monitoring, 3, '0', STR_PAD_LEFT),
                    'risk_name' => $risk->risk_event_deta ?? 'Risiko '.$risk->id_monitoring,
                ];
                $matrix[$rowIndex][$colIndex]['class'] = $this->getHeatmapCellClass($value);
            }
        }

        $riskList = $risks->take(10)->map(fn ($risk) => [
            'code' => 'R'.str_pad($risk->id_monitoring, 3, '0', STR_PAD_LEFT),
            'risk_name' => $risk->risk_event_deta ?? 'Risiko '.$risk->id_monitoring,
            'value' => $risk->value,
            'level' => $risk->levelRisiko->nama_level ?? 'Tidak Terdefinisi',
        ])->toArray();

        return [
            'rows' => $matrix,
            'risks' => $riskList,
            'total_risks' => $risks->count(),
        ];
    }

    private function getHeatmapCellClass(int $value): string
    {
        if ($value >= 20) {
            return 'bg-rose-100 ring-rose-200';
        }
        if ($value >= 15) {
            return 'bg-orange-100 ring-orange-200';
        }
        if ($value >= 10) {
            return 'bg-amber-100 ring-amber-200';
        }
        if ($value >= 5) {
            return 'bg-lime-100 ring-lime-200';
        }

        return 'bg-emerald-100 ring-emerald-200';
    }

    private function getDummyHeatmapData(): array
    {
        $dummyRisks = [
            ['code' => 'R001', 'risk_name' => 'Risiko Keuangan', 'value' => 20, 'level' => 'High'],
            ['code' => 'R002', 'risk_name' => 'Risiko Operasional', 'value' => 16, 'level' => 'Moderate to High'],
            ['code' => 'R003', 'risk_name' => 'Risiko Hukum', 'value' => 18, 'level' => 'High'],
            ['code' => 'R004', 'risk_name' => 'Risiko Teknologi', 'value' => 14, 'level' => 'Moderate to High'],
            ['code' => 'R005', 'risk_name' => 'Risiko SDM', 'value' => 12, 'level' => 'Moderate'],
        ];

        $matrix = [];
        for ($i = 0; $i < 5; $i++) {
            $row = [];
            for ($j = 0; $j < 5; $j++) {
                $value = ($i + 1) * ($j + 1);
                $row[] = ['value' => $value, 'class' => $this->getHeatmapCellClass($value), 'risks' => []];
            }
            $matrix[] = $row;
        }

        $dummyPositions = [[4, 4, 0], [3, 4, 1], [4, 3, 2], [2, 3, 3], [1, 2, 4]];
        foreach ($dummyPositions as $pos) {
            $row = $pos[0];
            $col = $pos[1];
            $index = $pos[2];
            if (isset($matrix[$row][$col]) && isset($dummyRisks[$index])) {
                $matrix[$row][$col]['risks'][] = [
                    'code' => $dummyRisks[$index]['code'],
                    'risk_name' => $dummyRisks[$index]['risk_name'],
                ];
            }
        }

        return ['rows' => $matrix, 'risks' => $dummyRisks, 'total_risks' => count($dummyRisks)];
    }

    public function getSmapData(): array
    {
        $levels = LevelRisiko::whereHas('smapMonitorings')->get();
        $pieData = ['labels' => [], 'inherent' => [], 'current' => [], 'target' => []];

        if ($levels->isNotEmpty()) {
            $pieData['labels'] = $levels->pluck('nama_level')->toArray();
            foreach ($levels as $level) {
                $pieData['inherent'][] = SmapMonitoring::where('id_level', $level->id_level)->whereNull('parent_id')->count();
                $pieData['current'][] = SmapMonitoring::where('id_level', $level->id_level)->whereNotNull('parent_id')->count();
                $pieData['target'][] = SmapMonitoring::where('id_level', $level->id_level)->whereNull('parent_id')->count();
            }
        } else {
            $pieData['labels'] = ['Low', 'Low to Moderate', 'Moderate', 'Moderate to High', 'High'];
            $pieData['inherent'] = [5, 8, 3];
            $pieData['current'] = [3, 5, 2];
            $pieData['target'] = [5, 8, 3];
        }

        $pieData['efektif'] = [
            'labels' => ['Pencatatan', 'Effective', 'Mostly Effective', 'Partially Effective', 'In-Effective', 'Unmeasurable'],
            'off' => [2, 4, 3, 2, 1, 1],
        ];

        $pieData['progres'] = [
            'labels' => ['Belum', 'Proses', 'Sudah'],
            'off' => [5, 8, 7],
        ];

        try {
            $komposisiData = $this->repository->getSmapKomposisiRaw();
            $units = $komposisiData->pluck('unit')->unique()->values()->toArray();
            $levelList = $komposisiData->pluck('level')->unique()->values()->toArray();

            $levelColors = [
                'High' => '#FF0100',
                'Moderate to High' => '#FFC000',
                'Moderate' => '#FFFF00',
                'Low to Moderate' => '#91D050',
                'Low' => '#03B050',
            ];

            $chartDatasets = [];
            foreach ($levelList as $lvl) {
                $dataByUnit = [];
                foreach ($units as $unit) {
                    $found = $komposisiData->firstWhere(fn ($item) => $item->unit == $unit && $item->level == $lvl);
                    $dataByUnit[] = $found ? $found->total : 0;
                }
                $chartDatasets[] = [
                    'label' => $lvl,
                    'data' => $dataByUnit,
                    'backgroundColor' => $levelColors[$lvl] ?? '#94a3b8',
                ];
            }
            $komposisi = ['labels' => $units, 'datasets' => $chartDatasets];
        } catch (\Exception $e) {
            $komposisi = [
                'labels' => ['Unit A', 'Unit B', 'Unit C', 'Unit D'],
                'datasets' => [
                    ['label' => 'High', 'data' => [2, 1, 3, 0], 'backgroundColor' => '#FF0100'],
                    ['label' => 'Moderate to High', 'data' => [3, 2, 1, 2], 'backgroundColor' => '#FFC000'],
                    ['label' => 'Moderate', 'data' => [1, 3, 2, 1], 'backgroundColor' => '#FFFF00'],
                    ['label' => 'Low to Moderate', 'data' => [0, 1, 0, 2], 'backgroundColor' => '#91D050'],
                    ['label' => 'Low', 'data' => [1, 0, 1, 0], 'backgroundColor' => '#03B050'],
                ],
            ];
        }

        try {
            $unitTable = $this->repository->getSmapUnitTableRaw();
        } catch (\Exception $e) {
            $unitTable = collect([
                (object) ['nama_unit' => 'Unit A', 'progress_belum' => 5, 'progress_proses' => 3, 'progress_sudah' => 2],
                (object) ['nama_unit' => 'Unit B', 'progress_belum' => 4, 'progress_proses' => 2, 'progress_sudah' => 4],
                (object) ['nama_unit' => 'Unit C', 'progress_belum' => 2, 'progress_proses' => 4, 'progress_sudah' => 3],
            ]);
        }

        try {
            $risks = SmapMonitoring::whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            $risks = collect([]);
        }

        return [
            'pieData' => $pieData,
            'risks' => $risks,
            'unitTable' => $unitTable,
            'komposisi' => $komposisi,
        ];
    }

    public function getDepartemenChartData(): array
    {
        try {
            $units = $this->repository->getDepartemenUnitsRaw();
            $labels = $units->pluck('nama_unit')->toArray();
            $data = $units->pluck('total_risiko')->map(fn ($v) => (int) $v)->toArray();
        } catch (\Exception $e) {
            $labels = ['Belum Ada Data'];
            $data = [0];
        }

        $levels = LevelRisiko::all();
        $levelLabels = $levels->pluck('nama_level')->toArray() ?: ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'];

        $inherentData = [];
        $currentData = [];
        $targetData = [];

        foreach ($levels as $level) {
            $inherentData[] = DepMonitoring::where('id_level', $level->id_level)->count();
            try {
                $currentCount = DB::table('dep_monitoring_periods')
                    ->join('dep_monitoring', 'dep_monitoring_periods.id_monitoring', '=', 'dep_monitoring.id_monitoring')
                    ->where('dep_monitoring.id_level', $level->id_level)
                    ->where(fn ($q) => $q->where('dep_monitoring_periods.progres_belum', '>', 0)->orWhere('dep_monitoring_periods.progres_proses', '>', 0))
                    ->distinct('dep_monitoring_periods.id_monitoring')
                    ->count('dep_monitoring_periods.id_monitoring');

                $targetCount = DB::table('dep_monitoring_periods')
                    ->join('dep_monitoring', 'dep_monitoring_periods.id_monitoring', '=', 'dep_monitoring.id_monitoring')
                    ->where('dep_monitoring.id_level', $level->id_level)
                    ->where('dep_monitoring_periods.progres_sudah', '>', 0)
                    ->distinct('dep_monitoring_periods.id_monitoring')
                    ->count('dep_monitoring_periods.id_monitoring');
            } catch (\Exception $e) {
                $total = DepMonitoring::where('id_level', $level->id_level)->count();
                $currentCount = (int) ($total * 0.6);
                $targetCount = (int) ($total * 0.4);
            }
            $currentData[] = $currentCount;
            $targetData[] = $targetCount;
        }

        if (empty($inherentData) || array_sum($inherentData) == 0) {
            $inherentData = [5, 8, 6, 4, 2];
            $currentData = [3, 5, 4, 2, 1];
            $targetData = [2, 3, 2, 2, 1];
        }

        $jenisRisikoData = [4, 6];
        try {
            if (Schema::hasColumn('dep_monitoring', 'type')) {
                $jenisRisikoData = [
                    DepMonitoring::where('type', 'Proyek')->count(),
                    DepMonitoring::where('type', 'Non-Proyek')->count(),
                ];
            }
        } catch (\Exception $e) {
        }

        $efektifRisikoData = [2, 3, 2, 1, 1, 1];
        try {
            $progresData = [
                DB::table('dep_monitoring_periods')->sum('progres_belum'),
                DB::table('dep_monitoring_periods')->sum('progres_proses'),
                DB::table('dep_monitoring_periods')->sum('progres_sudah'),
            ];
        } catch (\Exception $e) {
            $progresData = [5, 8, 7];
        }

        try {
            $progresPerUnit = $this->repository->getDepartemenProgresPerUnitRaw()
                ->map(fn ($item) => [
                    'nama_unit' => $item->nama_unit ?? 'Unit',
                    'belum' => (int) $item->belum,
                    'proses' => (int) $item->proses,
                    'sudah' => (int) $item->sudah,
                ])->toArray();
        } catch (\Exception $e) {
            $progresPerUnit = [
                ['nama_unit' => 'Unit A', 'belum' => 5, 'proses' => 3, 'sudah' => 2],
                ['nama_unit' => 'Unit B', 'belum' => 4, 'proses' => 2, 'sudah' => 4],
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'inherentData' => $inherentData,
            'currentData' => $currentData,
            'targetData' => $targetData,
            'levelLabels' => $levelLabels,
            'jenisRisikoData' => $jenisRisikoData,
            'efektifRisikoData' => $efektifRisikoData,
            'progresData' => $progresData,
            'progresPerUnit' => $progresPerUnit,
        ];
    }
}
