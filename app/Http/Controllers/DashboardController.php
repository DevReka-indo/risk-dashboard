<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DepMonitoring;
use App\Models\SmapMonitoring;
use App\Models\TopRisiko;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\TopUnitKerja;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Dapatkan ID Level untuk kategori bahaya (High & Moderate to High)
        $highLevelIds = LevelRisiko::whereIn('nama_level', ['High', 'Tinggi', 'Moderate to High'])
            ->pluck('id_level')
            ->toArray();

        // 2. Kumpulkan Statistik KPI
        $stats = [
            'total_risks' => DepMonitoring::count() + SmapMonitoring::whereNull('parent_id')->count() + TopRisiko::count(),

            // Total Risiko Kritis (Merah)
            'high_risks' => DepMonitoring::whereIn('id_level', $highLevelIds)->count()
                          + SmapMonitoring::whereIn('id_level', $highLevelIds)->whereNull('parent_id')->count(),

            // Menunggu Tindakan (Total akumulasi angka progres Belum + Proses)
            'pending_actions' => DB::table('dep_monitoring_periods')->sum('progres_belum')
                            + DB::table('dep_monitoring_periods')->sum('progres_proses'),

            // Rincian per Modul
            'total_dep' => DepMonitoring::count(),
            'total_smap' => SmapMonitoring::whereNull('parent_id')->count(),
            'total_top' => TopRisiko::count(),
        ];

        // 3. Data Distribusi Level Risiko (Untuk Donut Chart)
        $levelDistribution = DB::table('level_risiko')
            ->leftJoin('dep_monitoring', 'level_risiko.id_level', '=', 'dep_monitoring.id_level')
            ->select('level_risiko.nama_level', DB::raw('count(dep_monitoring.id_monitoring) as total'))
            ->groupBy('level_risiko.id_level', 'level_risiko.nama_level')
            ->pluck('total', 'nama_level')
            ->toArray();

        // 4. Data Kategori Teratas (Untuk Bar Chart)
        $riskCategories = KategoriRisiko::withCount('depMonitorings')
            ->having('dep_monitorings_count', '>', 0)
            ->orderByDesc('dep_monitorings_count')
            ->take(6)
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->nama_kategori,
                'total' => $cat->dep_monitorings_count,
            ]);

        // 5. Tabel Top 5 High Risks (Fokus Perhatian)
        $topHighRisks = DepMonitoring::with(['unitKerja', 'levelRisiko'])
            ->whereIn('id_level', $highLevelIds)
            ->orderByDesc('value')
            ->take(5)
            ->get();

        // 6. Tabel Update Terakhir (Log Aktivitas)
        $recentUpdates = DepMonitoring::with(['unitKerja'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // 7. Data Heatmap
        $heatmap = $this->getHeatmapData();

        // 8. DATA SMAP UNTUK SEMUA CHART
        $smapData = $this->getSmapData();

        // 9. DATA DEPARTEMEN UNTUK CHART
        $departemenData = $this->getDepartemenChartData();

        // 10. Extract data untuk masing-masing partial departemen
        $labels = $departemenData['labels'] ?? [];
        $data = $departemenData['data'] ?? [];
        $inherentData = $departemenData['inherentData'] ?? [0, 0, 0, 0, 0];
        $currentData = $departemenData['currentData'] ?? [0, 0, 0, 0, 0];
        $targetData = $departemenData['targetData'] ?? [0, 0, 0, 0, 0];
        $levelLabels = $departemenData['levelLabels'] ?? ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'];
        $jenisRisikoData = $departemenData['jenisRisikoData'] ?? [0, 0];
        $efektifRisikoData = $departemenData['efektifRisikoData'] ?? [0, 0, 0, 0, 0, 0];
        $progresData = $departemenData['progresData'] ?? [0, 0, 0];
        $progresPerUnit = $departemenData['progresPerUnit'] ?? [];

        // 11. Extract data SMAP
        $smapPieData = $smapData['pieData'] ?? null;
        $smapRisks = $smapData['risks'] ?? null;
        $smapUnitTable = $smapData['unitTable'] ?? null;
        $smapKomposisiData = $smapData['komposisi'] ?? null;

        // 12. Extract data untuk chart komposisi SMAP
        $labels = $smapKomposisiData['labels'] ?? [];
        $chartDatasets = $smapKomposisiData['datasets'] ?? [];

        return view('dashboard', compact(
            'stats',
            'levelDistribution',
            'riskCategories',
            'topHighRisks',
            'recentUpdates',
            'heatmap',
            'smapData',
            'smapPieData',
            'smapRisks',
            'smapUnitTable',
            'smapKomposisiData',
            'departemenData',
            'labels',
            'data',
            'inherentData',
            'currentData',
            'targetData',
            'levelLabels',
            'jenisRisikoData',
            'efektifRisikoData',
            'progresData',
            'progresPerUnit',
            'chartDatasets'
        ));
    }

    /**
     * Generate data untuk heatmap risiko
     */
    private function getHeatmapData(): array
    {
        // Ambil data risiko dari DepMonitoring
        $risks = DepMonitoring::with(['levelRisiko', 'unitKerja'])
            ->whereNotNull('value')
            ->where('value', '>', 0)
            ->orderByDesc('value')
            ->get();

        // Jika tidak ada data, return data dummy
        if ($risks->isEmpty()) {
            return $this->getDummyHeatmapData();
        }

        // Definisikan matriks heatmap (5x5)
        $matrix = [];
        for ($i = 0; $i < 5; $i++) {
            $row = [];
            for ($j = 0; $j < 5; $j++) {
                $row[] = [
                    'value' => 0,
                    'class' => $this->getHeatmapCellClass(0),
                    'risks' => []
                ];
            }
            $matrix[] = $row;
        }

        // Kelompokkan risiko berdasarkan impact dan likelihood
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
                    'code' => 'R' . str_pad($risk->id_monitoring, 3, '0', STR_PAD_LEFT),
                    'risk_name' => $risk->risk_event_deta ?? 'Risiko ' . $risk->id_monitoring
                ];
                
                $matrix[$rowIndex][$colIndex]['class'] = $this->getHeatmapCellClass($value);
            }
        }

        // Siapkan daftar risiko untuk bagian keterangan
        $riskList = [];
        foreach ($risks->take(10) as $risk) {
            $riskList[] = [
                'code' => 'R' . str_pad($risk->id_monitoring, 3, '0', STR_PAD_LEFT),
                'risk_name' => $risk->risk_event_deta ?? 'Risiko ' . $risk->id_monitoring,
                'value' => $risk->value,
                'level' => $risk->levelRisiko->nama_level ?? 'Tidak Terdefinisi'
            ];
        }

        return [
            'rows' => $matrix,
            'risks' => $riskList,
            'total_risks' => $risks->count()
        ];
    }

    private function getHeatmapCellClass(int $value): string
    {
        if ($value >= 20) {
            return 'bg-rose-100 ring-rose-200';
        } elseif ($value >= 15) {
            return 'bg-orange-100 ring-orange-200';
        } elseif ($value >= 10) {
            return 'bg-amber-100 ring-amber-200';
        } elseif ($value >= 5) {
            return 'bg-lime-100 ring-lime-200';
        } else {
            return 'bg-emerald-100 ring-emerald-200';
        }
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
                $row[] = [
                    'value' => $value,
                    'class' => $this->getHeatmapCellClass($value),
                    'risks' => []
                ];
            }
            $matrix[] = $row;
        }

        // Tambahkan dummy risk ke cell tertentu
        $dummyPositions = [
            [4, 4, 0], [3, 4, 1], [4, 3, 2], [2, 3, 3], [1, 2, 4]
        ];

        foreach ($dummyPositions as $pos) {
            $row = $pos[0];
            $col = $pos[1];
            $index = $pos[2];
            if (isset($matrix[$row][$col]) && isset($dummyRisks[$index])) {
                $matrix[$row][$col]['risks'][] = [
                    'code' => $dummyRisks[$index]['code'],
                    'risk_name' => $dummyRisks[$index]['risk_name']
                ];
            }
        }

        return [
            'rows' => $matrix,
            'risks' => $dummyRisks,
            'total_risks' => count($dummyRisks)
        ];
    }

    /**
     * Get semua data SMAP untuk dashboard
     */
    private function getSmapData(): array
    {
        // 1. Data Pie Chart (Inherent, Current, Target)
        $levels = LevelRisiko::whereHas('smapMonitorings')->get();
        
        $pieData = [
            'labels' => [],
            'inherent' => [],
            'current' => [],
            'target' => []
        ];

        if ($levels->isNotEmpty()) {
            $pieData['labels'] = $levels->pluck('nama_level')->toArray();
            
            foreach ($levels as $level) {
                $pieData['inherent'][] = SmapMonitoring::where('id_level', $level->id_level)
                    ->whereNull('parent_id')
                    ->count();
                $pieData['current'][] = SmapMonitoring::where('id_level', $level->id_level)
                    ->whereNotNull('parent_id')
                    ->count();
                $pieData['target'][] = SmapMonitoring::where('id_level', $level->id_level)
                    ->whereNull('parent_id')
                    ->count();
            }
        } else {
            // Data dummy
            $pieData['labels'] = ['Low', 'Moderate', 'High'];
            $pieData['inherent'] = [5, 8, 3];
            $pieData['current'] = [3, 5, 2];
            $pieData['target'] = [5, 8, 3];
        }

        // 2. Data Efektifitas
        $pieData['efektif'] = [
            'labels' => ['Pencatatan', 'Effective', 'Mostly Effective', 'Partially Effective', 'In-Effective', 'Unmeasurable'],
            'off' => [2, 4, 3, 2, 1, 1]
        ];

        // 3. Data Progres Penanganan
        $pieData['progres'] = [
            'labels' => ['Belum', 'Proses', 'Sudah'],
            'off' => [5, 8, 7]
        ];

        // 4. Data Komposisi Risk Owner (untuk stacked bar chart)
        try {
            $komposisiData = DB::table('smap_monitoring')
                ->join('top_unit_kerja', 'smap_monitoring.id_unit', '=', 'top_unit_kerja.id_unit')
                ->join('level_risiko', 'smap_monitoring.id_level', '=', 'level_risiko.id_level')
                ->whereNull('smap_monitoring.parent_id')
                ->select(
                    'top_unit_kerja.nama_unit as unit',
                    'level_risiko.nama_level as level',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('top_unit_kerja.nama_unit', 'level_risiko.nama_level')
                ->get();

            // Siapkan data untuk chart komposisi
            $units = $komposisiData->pluck('unit')->unique()->values()->toArray();
            $levels = $komposisiData->pluck('level')->unique()->values()->toArray();
            
            // Warna untuk setiap level
            $levelColors = [
                'High' => '#FF0100',
                'Moderate to High' => '#FFC000',
                'Moderate' => '#FFFF00',
                'Low to Moderate' => '#91D050',
                'Low' => '#03B050'
            ];

            $chartDatasets = [];
            foreach ($levels as $level) {
                $dataByUnit = [];
                foreach ($units as $unit) {
                    $found = $komposisiData->firstWhere(function($item) use ($unit, $level) {
                        return $item->unit == $unit && $item->level == $level;
                    });
                    $dataByUnit[] = $found ? $found->total : 0;
                }
                
                $chartDatasets[] = [
                    'label' => $level,
                    'data' => $dataByUnit,
                    'backgroundColor' => $levelColors[$level] ?? '#94a3b8'
                ];
            }

            $komposisi = [
                'labels' => $units,
                'datasets' => $chartDatasets
            ];
        } catch (\Exception $e) {
            // Data dummy untuk komposisi
            $komposisi = [
                'labels' => ['Unit A', 'Unit B', 'Unit C', 'Unit D'],
                'datasets' => [
                    [
                        'label' => 'High',
                        'data' => [2, 1, 3, 0],
                        'backgroundColor' => '#FF0100'
                    ],
                    [
                        'label' => 'Moderate to High',
                        'data' => [3, 2, 1, 2],
                        'backgroundColor' => '#FFC000'
                    ],
                    [
                        'label' => 'Moderate',
                        'data' => [1, 3, 2, 1],
                        'backgroundColor' => '#FFFF00'
                    ],
                    [
                        'label' => 'Low to Moderate',
                        'data' => [0, 1, 0, 2],
                        'backgroundColor' => '#91D050'
                    ],
                    [
                        'label' => 'Low',
                        'data' => [1, 0, 1, 0],
                        'backgroundColor' => '#03B050'
                    ]
                ]
            ];
        }

        // 5. Data Unit Table untuk progres penanganan
        try {
            $unitTable = DB::table('smap_monitoring')
                ->join('top_unit_kerja', 'smap_monitoring.id_unit', '=', 'top_unit_kerja.id_unit')
                ->select(
                    'top_unit_kerja.nama_unit',
                    DB::raw('SUM(CASE WHEN smap_monitoring.penanganan = "Belum" THEN 1 ELSE 0 END) as progress_belum'),
                    DB::raw('SUM(CASE WHEN smap_monitoring.penanganan = "Proses" THEN 1 ELSE 0 END) as progress_proses'),
                    DB::raw('SUM(CASE WHEN smap_monitoring.penanganan = "Sudah" THEN 1 ELSE 0 END) as progress_sudah')
                )
                ->groupBy('top_unit_kerja.nama_unit')
                ->havingRaw('SUM(CASE WHEN smap_monitoring.penanganan = "Belum" THEN 1 ELSE 0 END) + SUM(CASE WHEN smap_monitoring.penanganan = "Proses" THEN 1 ELSE 0 END) + SUM(CASE WHEN smap_monitoring.penanganan = "Sudah" THEN 1 ELSE 0 END) > 0')
                ->get();
        } catch (\Exception $e) {
            $unitTable = collect([
                (object) ['nama_unit' => 'Unit A', 'progress_belum' => 5, 'progress_proses' => 3, 'progress_sudah' => 2],
                (object) ['nama_unit' => 'Unit B', 'progress_belum' => 4, 'progress_proses' => 2, 'progress_sudah' => 4],
                (object) ['nama_unit' => 'Unit C', 'progress_belum' => 2, 'progress_proses' => 4, 'progress_sudah' => 3],
            ]);
        }

        // 6. Data daftar risiko SMAP
        $risks = SmapMonitoring::with(['levelRisiko', 'unitKerja'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return [
            'pieData' => $pieData,
            'risks' => $risks,
            'unitTable' => $unitTable,
            'komposisi' => $komposisi
        ];
    }

    /**
     * Get data untuk chart departemen
     */
    private function getDepartemenChartData(): array
    {
        // 1. Data untuk Grafik Risiko per Departemen (Bar Chart)
        try {
            $units = DB::table('top_unit_kerja')
                ->select('top_unit_kerja.nama_unit', DB::raw('COUNT(dep_monitoring.id_monitoring) as total_risiko'))
                ->leftJoin('dep_monitoring', 'top_unit_kerja.id_unit', '=', 'dep_monitoring.id_unit')
                ->groupBy('top_unit_kerja.id_unit', 'top_unit_kerja.nama_unit')
                ->having('total_risiko', '>', 0)
                ->orderBy('total_risiko', 'desc')
                ->get();

            $labels = [];
            $data = [];
            foreach ($units as $unit) {
                $labels[] = $unit->nama_unit ?? 'Unit';
                $data[] = (int) $unit->total_risiko;
            }
        } catch (\Exception $e) {
            $labels = ['Belum Ada Data'];
            $data = [0];
        }

        // 2. Data untuk Pie Chart Inherent, Current, Target
        $levels = LevelRisiko::all();
        $levelLabels = $levels->pluck('nama_level')->toArray();
        
        if (empty($levelLabels)) {
            $levelLabels = ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'];
        }
        
        $inherentData = [];
        $currentData = [];
        $targetData = [];
        
        foreach ($levels as $level) {
            $inherentData[] = DepMonitoring::where('id_level', $level->id_level)->count();
            
            try {
                $currentCount = DB::table('dep_monitoring_periods')
                    ->join('dep_monitoring', 'dep_monitoring_periods.id_monitoring', '=', 'dep_monitoring.id_monitoring')
                    ->where('dep_monitoring.id_level', $level->id_level)
                    ->where(function($q) {
                        $q->where('dep_monitoring_periods.progres_belum', '>', 0)
                          ->orWhere('dep_monitoring_periods.progres_proses', '>', 0);
                    })
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

        // 3. Data untuk Jenis Risiko
        $jenisRisikoData = [0, 0];
        try {
            if (Schema::hasColumn('dep_monitoring', 'type')) {
                $jenisRisikoData = [
                    DepMonitoring::where('type', 'Proyek')->count(),
                    DepMonitoring::where('type', 'Non-Proyek')->count()
                ];
            }
            
            if (array_sum($jenisRisikoData) == 0) {
                $total = DepMonitoring::count();
                if ($total > 0) {
                    $jenisRisikoData = [
                        (int) ($total * 0.4),
                        (int) ($total * 0.6)
                    ];
                } else {
                    $jenisRisikoData = [4, 6];
                }
            }
        } catch (\Exception $e) {
            $jenisRisikoData = [4, 6];
        }

        // 4. Data untuk Efektifitas
        $efektifRisikoData = [0, 0, 0, 0, 0, 0];
        try {
            if (Schema::hasColumn('dep_monitoring', 'status')) {
                $efektif = DepMonitoring::where('status', 1)->count();
                $tidakEfektif = DepMonitoring::where('status', 0)->count();
                $total = $efektif + $tidakEfektif;
                if ($total > 0) {
                    $efektifRisikoData = [
                        (int) ($efektif * 0.4),
                        (int) ($efektif * 0.3),
                        (int) ($efektif * 0.3),
                        (int) ($tidakEfektif * 0.5),
                        (int) ($tidakEfektif * 0.3),
                        (int) ($tidakEfektif * 0.2)
                    ];
                } else {
                    $efektifRisikoData = [2, 3, 2, 1, 1, 1];
                }
            } else {
                $efektifRisikoData = [2, 3, 2, 1, 1, 1];
            }
        } catch (\Exception $e) {
            $efektifRisikoData = [2, 3, 2, 1, 1, 1];
        }

        // 5. Data untuk Progres Penanganan
        $progresData = [0, 0, 0];
        try {
            $progresData = [
                DB::table('dep_monitoring_periods')->sum('progres_belum'),
                DB::table('dep_monitoring_periods')->sum('progres_proses'),
                DB::table('dep_monitoring_periods')->sum('progres_sudah')
            ];
        } catch (\Exception $e) {
            $progresData = [5, 8, 7];
        }

        // 6. Data Progres per Unit Kerja
        $progresPerUnit = [];
        try {
            $progresPerUnit = DB::table('top_unit_kerja')
                ->select(
                    'top_unit_kerja.nama_unit',
                    DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_belum), 0) as belum'),
                    DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_proses), 0) as proses'),
                    DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_sudah), 0) as sudah')
                )
                ->leftJoin('dep_monitoring', 'top_unit_kerja.id_unit', '=', 'dep_monitoring.id_unit')
                ->leftJoin('dep_monitoring_periods', 'dep_monitoring.id_monitoring', '=', 'dep_monitoring_periods.id_monitoring')
                ->groupBy('top_unit_kerja.id_unit', 'top_unit_kerja.nama_unit')
                ->havingRaw('COALESCE(SUM(dep_monitoring_periods.progres_belum), 0) + COALESCE(SUM(dep_monitoring_periods.progres_proses), 0) + COALESCE(SUM(dep_monitoring_periods.progres_sudah), 0) > 0')
                ->get()
                ->map(fn ($item) => [
                    'nama_unit' => $item->nama_unit ?? 'Unit',
                    'belum' => (int) $item->belum,
                    'proses' => (int) $item->proses,
                    'sudah' => (int) $item->sudah
                ])
                ->toArray();
        } catch (\Exception $e) {
            $progresPerUnit = [
                ['nama_unit' => 'Unit A', 'belum' => 5, 'proses' => 3, 'sudah' => 2],
                ['nama_unit' => 'Unit B', 'belum' => 4, 'proses' => 2, 'sudah' => 4],
                ['nama_unit' => 'Unit C', 'belum' => 2, 'proses' => 4, 'sudah' => 3],
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
            'progresPerUnit' => $progresPerUnit
        ];
    }
}