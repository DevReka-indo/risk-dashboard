<?php

namespace App\Services;

use App\Models\TopUnitKerja;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\Support\Facades\DB;

class DepartemenRiskDashboardService
{
    /**
     * Mengambil dan memproses semua data untuk halaman Dashboard
     */
    public function getDashboardData($selectedPeriode, $selectedYear)
    {
        $quarterMap = [1 => 'TW1', 2 => 'TW2', 3 => 'TW3', 4 => 'TW4'];
        $twString = $quarterMap[$selectedPeriode] ?? null;

        $allUnits = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $allLevels = LevelRisiko::orderBy('id_level', 'asc')->get();
        $allCategories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();

        // Query dasar mengambil history pivot periode yang dipilih
        $baseQuery = DB::table('dep_monitoring_periods')
            ->join('dep_monitoring', 'dep_monitoring_periods.id_monitoring', '=', 'dep_monitoring.id_monitoring')
            ->where('dep_monitoring_periods.year', $selectedYear);

        if ($selectedPeriode !== 'all' && $twString) {
            $baseQuery->where('dep_monitoring_periods.quarter', $twString);
        }

        // 1. Perhitungan Metrik Utama
        $totalRisiko = (clone $baseQuery)->count(DB::raw('DISTINCT dep_monitoring.id_monitoring'));
        $risikoAktif = (clone $baseQuery)->where('dep_monitoring.status', 1)->count(DB::raw('DISTINCT dep_monitoring.id_monitoring'));

        // 2. Pengelompokan Data Dasar
        $risksPerDept = (clone $baseQuery)->selectRaw('dep_monitoring.id_unit, count(DISTINCT dep_monitoring.id_monitoring) as total')
            ->groupBy('dep_monitoring.id_unit')->pluck('total', 'id_unit')->toArray();
        $risksPerLevel = (clone $baseQuery)->selectRaw('dep_monitoring_periods.id_level, count(*) as total')
            ->groupBy('dep_monitoring_periods.id_level')->pluck('total', 'id_level')->toArray();
        $risksPerCategory = (clone $baseQuery)->selectRaw('dep_monitoring.id_kategori, count(DISTINCT dep_monitoring.id_monitoring) as total')
            ->groupBy('dep_monitoring.id_kategori')->pluck('total', 'id_kategori')->toArray();
        $risksPerTrend = (clone $baseQuery)->selectRaw('dep_monitoring_periods.trend, count(*) as total')
            ->groupBy('dep_monitoring_periods.trend')->pluck('total', 'trend')->toArray();

        // 3. Logika Pembuatan Datasets Matrix
        $labels = [];
        $data = [];
        $stackedTemplates = [];
        $colorMapping = [
            'High'             => '#ef4444',
            'Moderate to High' => '#f59e0b',
            'Moderate'         => '#eab308',
            'Low to Moderate'  => '#3b82f6',
            'Low'              => '#10b981'
        ];

        foreach ($allLevels as $level) {
            $levelName = $level->nama_level ?? $level->level;
            $stackedTemplates[$level->id_level] = [
                'label' => $levelName,
                'backgroundColor' => $colorMapping[$levelName] ?? '#cbd5e1',
                'data' => []
            ];
        }

        foreach ($allUnits as $unit) {
            $totalUnitRisks = $risksPerDept[$unit->id_unit] ?? 0;
            $labels[] = $unit->nama_unit;
            $data[] = $totalUnitRisks;

            $currentDeptRisks = [];
            if ($totalUnitRisks > 0) {
                $currentDeptRisks = (clone $baseQuery)->where('dep_monitoring.id_unit', $unit->id_unit)
                    ->selectRaw('dep_monitoring_periods.id_level, count(*) as total')
                    ->groupBy('dep_monitoring_periods.id_level')->pluck('total', 'id_level')->toArray();
            }

            foreach ($allLevels as $level) {
                $stackedTemplates[$level->id_level]['data'][] = $currentDeptRisks[$level->id_level] ?? 0;
            }
        }
        $chartDatasets = array_values($stackedTemplates);

        // 4. Distribusi Persentase Level
        $maxLevelCount = 0;
        $levelDistributionData = [];
        foreach ($allLevels as $level) {
            $count = $risksPerLevel[$level->id_level] ?? 0;
            $maxLevelCount = max($maxLevelCount, $count);
            $levelDistributionData[] = ['name' => $level->nama_level ?? $level->level, 'count' => $count];
        }
        foreach ($levelDistributionData as &$item) {
            $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
        }

        // 5. Data Trend & Kategori
        $trendLabels = ['Naik', 'Turun', 'Stagnan'];
        $trendData = [
            ($risksPerTrend['Naik'] ?? $risksPerTrend['naik'] ?? 0),
            ($risksPerTrend['Turun'] ?? $risksPerTrend['turun'] ?? 0),
            (($risksPerTrend['Stagnan'] ?? 0) + ($risksPerTrend['stagnan'] ?? 0) + ($risksPerTrend['Stabil'] ?? 0) + ($risksPerTrend['stabil'] ?? 0)),
        ];

        $catLabels = []; $catData = [];
        foreach ($allCategories as $category) {
            $catLabels[] = $category->nama_kategori;
            $catData[] = $risksPerCategory[$category->id_kategori] ?? 0;
        }

        // 6. Matrix Proyek / Non-Proyek
        $proyekData = []; $nonProyekData = [];
        $typeMatrixQuery = (clone $baseQuery)->selectRaw('dep_monitoring_periods.id_level, dep_monitoring.type, count(DISTINCT dep_monitoring.id_monitoring) as total')
            ->groupBy('dep_monitoring_periods.id_level', 'dep_monitoring.type')->get();

        foreach ($typeMatrixQuery as $row) {
            if (strtolower($row->type) === 'proyek') {
                $proyekData[$row->id_level] = $row->total;
            } else {
                $nonProyekData[$row->id_level] = $row->total;
            }
        }

        $levelOrder = ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'];
        $matrixTypeData = [];
        foreach ($levelOrder as $levelName) {
            $level = $allLevels->firstWhere('nama_level', $levelName) ?? $allLevels->firstWhere('level', $levelName);
            $lvlId = $level ? $level->id_level : null;
            $matrixTypeData[] = [
                'level_name' => $levelName,
                'proyek'     => $lvlId ? ($proyekData[$lvlId] ?? 0) : 0,
                'non_proyek' => $lvlId ? ($nonProyekData[$lvlId] ?? 0) : 0,
            ];
        }

        // 7. Jenis Risiko (Proyek VS Non-Proyek)
        $risksByType = (clone $baseQuery)->selectRaw('dep_monitoring.type, count(DISTINCT dep_monitoring.id_monitoring) as total')
            ->groupBy('dep_monitoring.type')->pluck('total', 'type')->toArray();
        $jenisRisikoData = [
            (int) ($risksByType['Proyek'] ?? $risksByType['PROYEK'] ?? 0),
            (int) ($risksByType['Non-Proyek'] ?? $risksByType['Non Proyek'] ?? $risksByType['NON PROYEK'] ?? 0)
        ];

        // 8. Efektivitas Risiko
        $risksByEfektif = (clone $baseQuery)->selectRaw('dep_monitoring_periods.efektif_risiko, COUNT(*) as total')
            ->groupBy('dep_monitoring_periods.efektif_risiko')->pluck('total', 'efektif_risiko')->toArray();
        $efektifRisikoData = [
            (int) ($risksByEfektif['Effective'] ?? 0),
            (int) ($risksByEfektif['Mostly Effective'] ?? 0),
            (int) ($risksByEfektif['Partially Effective'] ?? 0),
            (int) ($risksByEfektif['In-Effective'] ?? 0),
            (int) ($risksByEfektif['Pencatatan'] ?? 0),
            (int) ($risksByEfektif['Unmeasurable'] ?? 0),
        ];

        return [
            'dashboardData' => [
                'summary' => [
                    'total_risiko'      => $totalRisiko,
                    'risiko_aktif'      => $risikoAktif,
                    'jumlah_departemen' => $allUnits->count(),
                ],
                'period' => $selectedPeriode === 'all' ? "Semua Triwulan - {$selectedYear}" : "Triwulan {$selectedPeriode} - {$selectedYear}",
                'level_distribution' => $levelDistributionData,
            ],
            'labels' => $labels,
            'data' => $data,
            'chartDatasets' => $chartDatasets,
            'catLabels' => $catLabels,
            'catData' => $catData,
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'matrixTypeData' => $matrixTypeData,
            'jenisRisikoData' => $jenisRisikoData,
            'efektifRisikoData' => $efektifRisikoData,
            'periodDisplay' => $selectedPeriode === 'all' ? "Semua Triwulan - {$selectedYear}" : "Triwulan {$selectedPeriode} - {$selectedYear}",

            // Panggil Fungsi Bantuan Chart
            'currentData' => $this->getPieChartData($baseQuery, 'dep_monitoring_periods.id_level'),
            'targetData' => $this->getPieChartData($baseQuery, 'dep_monitoring_periods.target_id_level'),
            'inherentData' => $this->getInherentChartData($baseQuery),
            'progresData' => $this->getProgresPenangananData($baseQuery),
        ];
    }

    /**
     * Kalkulasi status efektivitas risiko secara otomatis
     * Dipanggil saat Insert/Update nilai Triwulan
     */
    public function hitungEfektivitasRisiko($currentScore, $inherentScore, $currentLevel, $inherentLevel)
    {
        if ($currentScore == $inherentScore && in_array($currentLevel, [1, 2])) {
            return 'Pencatatan';
        } elseif ($currentScore < $inherentScore && in_array($currentLevel, [1, 2])) {
            return 'Effective';
        } elseif ($currentScore < $inherentScore && in_array($currentLevel, [3, 4, 5])) {
            return 'Mostly Effective';
        } elseif ($currentScore == $inherentScore && $currentLevel < $inherentLevel) {
            return 'Partially Effective';
        } elseif ($currentScore >= $inherentScore && $currentLevel >= $inherentLevel) {
            return 'In-Effective';
        }

        return 'Unmeasurable';
    }

    // --- FUNGSI BANTUAN CHART PRIVATE --- //

    private function getPieChartData($baseQuery, $columnName)
    {
        try {
            $data = (clone $baseQuery)->select($columnName, DB::raw('count(*) as total'))
                ->groupBy($columnName)->pluck('total', $columnName)->toArray();

            return [
                (int) ($data[5] ?? 0), (int) ($data[4] ?? 0), (int) ($data[3] ?? 0),
                (int) ($data[2] ?? 0), (int) ($data[1] ?? 0),
            ];
        } catch (\Exception $e) { return [0, 0, 0, 0, 0]; }
    }



    private function getInherentChartData($baseQuery)
    {
        try {
            $data = (clone $baseQuery)->select('dep_monitoring.id_level', DB::raw('count(DISTINCT dep_monitoring.id_monitoring) as total'))
                ->groupBy('dep_monitoring.id_level')->pluck('total', 'id_level')->toArray();

            return [
                (int) ($data[5] ?? 0), (int) ($data[4] ?? 0), (int) ($data[3] ?? 0),
                (int) ($data[2] ?? 0), (int) ($data[1] ?? 0),
            ];
        } catch (\Exception $e) { return [0, 0, 0, 0, 0]; }
    }

    private function getProgresPenangananData($baseQuery)
    {
        try {
            $data = (clone $baseQuery)->select('dep_monitoring_periods.penanganan as status_penanganan', DB::raw('count(*) as total'))
                ->groupBy('dep_monitoring_periods.penanganan')->pluck('total', 'status_penanganan')->toArray();

            return [
                (int) ($data['Belum'] ?? 0), (int) ($data['Proses'] ?? 0), (int) ($data['Sudah'] ?? 0),
            ];
        } catch (\Exception $e) { return [0, 0, 0]; }
    }
}
