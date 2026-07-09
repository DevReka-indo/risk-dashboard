<?php

namespace App\Http\Controllers;

use App\Models\TopUnitKerja;
use App\Models\DepMonitoring;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepartemenController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'data');

        // ==========================================
        // 1. LOGIKA UNTUK TAB DASHBOARD
        // ==========================================
        if ($tab === 'dashboard') {
            // Samakan dengan SMAP: Menggunakan integer periode 1-4
            $defaultQuarter = ceil(date('n') / 3);
            $selectedPeriode = (int) $request->query('periode', $defaultQuarter);
            $selectedYear = (int) $request->query('tahun', date('Y'));

            // Memetakan integer ke format string pivot tabel Departemen (TW1 - TW4)
            $quarterMap = [1 => 'TW1', 2 => 'TW2', 3 => 'TW3', 4 => 'TW4'];
            $twString = $quarterMap[$selectedPeriode] ?? 'TW1';

            $totalRisiko = 0;
            $risikoAktif = 0;

            $labels = [];
            $data = [];
            $catLabels = [];
            $catData = [];

            $levelDistributionData = [];
            $maxLevelCount = 0;

            $allUnits = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
            $allLevels = LevelRisiko::orderBy('id_level', 'asc')->get();
            $allCategories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();

            $trendLabels = ['Naik', 'Turun', 'Stagnan'];
            $trendData = [0, 0, 0];
            $chartDatasets = [];

            // Query dasar mengambil history pivot periode yang dipilih
            $baseDashboardQuery = DB::table('dep_monitoring_periods')
                ->join('dep_monitoring', 'dep_monitoring_periods.id_monitoring', '=', 'dep_monitoring.id_monitoring')
                ->where('dep_monitoring_periods.year', $selectedYear)
                ->where('dep_monitoring_periods.quarter', $twString);

            // Perhitungan Metrik
            $totalRisiko = (clone $baseDashboardQuery)->count(DB::raw('DISTINCT dep_monitoring.id_monitoring'));
            $risikoAktif = (clone $baseDashboardQuery)->where('dep_monitoring.status', 1)->count(DB::raw('DISTINCT dep_monitoring.id_monitoring'));

            $risksPerDept = (clone $baseDashboardQuery)
                ->selectRaw('dep_monitoring.id_unit, count(DISTINCT dep_monitoring.id_monitoring) as total')
                ->groupBy('dep_monitoring.id_unit')
                ->pluck('total', 'id_unit')
                ->toArray();

            $risksPerLevel = (clone $baseDashboardQuery)
                ->selectRaw('dep_monitoring_periods.id_level, count(*) as total')
                ->groupBy('dep_monitoring_periods.id_level')
                ->pluck('total', 'id_level')
                ->toArray();

            $risksPerCategory = (clone $baseDashboardQuery)
                ->selectRaw('dep_monitoring.id_kategori, count(DISTINCT dep_monitoring.id_monitoring) as total')
                ->groupBy('dep_monitoring.id_kategori')
                ->pluck('total', 'id_kategori')
                ->toArray();

            $risksPerTrend = (clone $baseDashboardQuery)
                ->selectRaw('dep_monitoring_periods.trend, count(*) as total')
                ->groupBy('dep_monitoring_periods.trend')
                ->pluck('total', 'trend')
                ->toArray();

            // Proteksi agar trend Stabil dan Stagnan digabung menjadi satu warna
            $trendData = [
                ($risksPerTrend['Naik'] ?? $risksPerTrend['naik'] ?? 0),
                ($risksPerTrend['Turun'] ?? $risksPerTrend['turun'] ?? 0),
                (($risksPerTrend['Stagnan'] ?? 0) + ($risksPerTrend['stagnan'] ?? 0) + ($risksPerTrend['Stabil'] ?? 0) + ($risksPerTrend['stabil'] ?? 0)),
            ];

            // Setup kerangka warna Stacked Chart
            $stackedTemplates = [];
            $colorMapping = [
                'High'             => '#ef4444', // Merah
                'Moderate to High' => '#f59e0b', // Kuning/Amber
                'Moderate'         => '#eab308', // Kuning terang
                'Low to Moderate'  => '#3b82f6', // Biru
                'Low'              => '#10b981'  // Hijau
            ];

            foreach ($allLevels as $level) {
                $levelName = $level->nama_level ?? $level->level;
                $stackedTemplates[$level->id_level] = [
                    'label' => $levelName,
                    'backgroundColor' => $colorMapping[$levelName] ?? '#cbd5e1',
                    'data' => []
                ];
            }

            // Loop Komposisi per Departemen
            foreach ($allUnits as $unit) {
                $totalUnitRisks = $risksPerDept[$unit->id_unit] ?? 0;
                $data[] = $totalUnitRisks;

                if ($totalUnitRisks > 0) {
                    $labels[] = $unit->nama_unit;

                    $currentDeptRisks = (clone $baseDashboardQuery)
                        ->where('dep_monitoring.id_unit', $unit->id_unit)
                        ->selectRaw('dep_monitoring_periods.id_level, count(*) as total')
                        ->groupBy('dep_monitoring_periods.id_level')
                        ->pluck('total', 'id_level')
                        ->toArray();

                    foreach ($allLevels as $level) {
                        $stackedTemplates[$level->id_level]['data'][] = $currentDeptRisks[$level->id_level] ?? 0;
                    }
                }
            }
            $chartDatasets = array_values($stackedTemplates);

            // Distribusi Bar Persentase Level
            foreach ($allLevels as $level) {
                $count = $risksPerLevel[$level->id_level] ?? 0;
                $maxLevelCount = max($maxLevelCount, $count);

                $levelDistributionData[] = [
                    'name'  => $level->nama_level ?? $level->level,
                    'count' => $count
                ];
            }
            foreach ($levelDistributionData as &$item) {
                $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
            }

            // Kategori Departemen
            foreach ($allCategories as $category) {
                $catLabels[] = $category->nama_kategori;
                $catData[] = $risksPerCategory[$category->id_kategori] ?? 0;
            }

            $dashboardData = [
                'summary' => [
                    'total_risiko'      => $totalRisiko,
                    'risiko_aktif'      => $risikoAktif,
                    'jumlah_departemen' => $allUnits->count(),
                ],
                'period' => "Triwulan {$selectedPeriode} - {$selectedYear}",
                'level_distribution' => $levelDistributionData,
            ];

            return view('departemen.index', compact(
                'tab',
                'selectedPeriode',
                'selectedYear',
                'dashboardData',
                'labels',
                'data',
                'chartDatasets',
                'catLabels',
                'catData',
                'trendLabels',
                'trendData'
            ));
        }

        // ==========================================
        // 2. LOGIKA UNTUK TAB DATA (Tabel List)
        // ==========================================
        $search     = $request->string('search')->toString();
        $unitId     = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId    = $request->string('level_id')->toString();
        $type       = $request->string('type')->toString();
        $status     = $request->string('status')->toString();

        $risks = DepMonitoring::query()
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'periods' => function($q) {
                $q->orderBy('year', 'desc')->orderBy('quarter', 'desc');
            }])
            ->when($search, fn($q) => $q->where('risk_event_deta', 'like', "%{$search}%"))
            ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
            ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
            ->when($levelId, fn($q) => $q->where('id_level', $levelId))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status !== '', fn($q) => $q->where('status', (bool) $status))
            ->oldest('id_monitoring')
            ->paginate(10)
            ->withQueryString();

        $units      = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels     = LevelRisiko::all();

        return view('departemen.index', compact(
            'tab', 'risks', 'search', 'unitId', 'categoryId', 'levelId',
            'type', 'status', 'units', 'categories', 'levels'
        ));
    }

    public function create(): View
    {
        $units      = TopUnitKerja::all();
        $categories = KategoriRisiko::all();
        $levels     = LevelRisiko::all();

        return view('departemen.create', compact('units', 'categories', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
        ]);

        $defaultLevel = DB::table('level_risiko')->orderBy('urutan', 'asc')->first();

        // Setter nilai awal sebelum ada riwayat triwulan
        $validated['value']    = 0;
        $validated['inherent'] = 0;
        $validated['trend']    = 'Stabil';
        $validated['id_level'] = $defaultLevel ? $defaultLevel->id_level : 1;

        DepMonitoring::create($validated);

        return redirect()
            ->route('department-risk.index', ['tab' => 'data'])
            ->with('success', 'Risk Department berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $risk       = DepMonitoring::findOrFail($id);
        $units      = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels     = LevelRisiko::all();

        return view('departemen.edit', compact('risk', 'units', 'categories', 'levels'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
        ]);

        $risk = DepMonitoring::findOrFail($id);
        $risk->update($validated);

        return redirect()
            ->route('department-risk.index', ['tab' => 'data'])
            ->with('success', 'Risk Department berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $risk = DepMonitoring::findOrFail($id);
        $risk->delete();

        return redirect()
            ->route('department-risk.index', ['tab' => 'data'])
            ->with('success', 'Risk Department berhasil dihapus.');
    }

    public function show(string $id): View
    {
        $risk   = DepMonitoring::with(['unitKerja', 'kategoriRisiko', 'periods' => function($q) {
            $q->orderBy('year', 'desc')->orderBy('quarter', 'desc');
        }])->findOrFail($id);

        $levels = LevelRisiko::orderBy('urutan', 'asc')->get();

        return view('departemen.show', compact('risk', 'levels'));
    }

    public function updatePeriod(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'quarter'  => ['required', 'in:TW1,TW2,TW3,TW4'],
            'year'     => ['required', 'integer', 'min:2020', 'max:2099'],
            'value'    => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'trend'    => ['required', 'string'],
            'calculated_level' => ['required', 'string'],
        ]);

        $risk = DepMonitoring::findOrFail($id);

        $isExist = DB::table('dep_monitoring_periods')
            ->where('id_monitoring', $id)
            ->where('quarter', $request->quarter)
            ->where('year', $request->year)
            ->exists();

        if ($isExist) {
            return redirect()->route('department-risk.show', $id)
                ->with('error', "Periode {$request->quarter} Tahun {$request->year} sudah terdaftar pada risiko ini.");
        }

        // Pencarian ID Level dinamis berdasarkan text (selaras dengan metode di SMAP)
        $levelStr = $request->calculated_level;
        // Ubah 'level' menjadi 'id_level'
        $levelRecord = LevelRisiko::where('nama_level', $levelStr)->orWhere('id_level', $levelStr)->first();
        $idLevelTerbaru = $levelRecord ? $levelRecord->id_level : $risk->id_level;

        // Attach Pivot
        $risk->periods()->attach($idLevelTerbaru, [
            'quarter'    => $request->quarter,
            'year'       => $request->year,
            'value'      => $request->value,
            'inherent'   => $request->inherent,
            'trend'      => $request->trend,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mengubah status parent risk sesuai data terbaru agar sinkron di index
        $risk->update([
            'id_level' => $idLevelTerbaru,
            'value'    => $request->value,
            'inherent' => $request->inherent,
            'trend'    => $request->trend,
        ]);

        return redirect()->route('department-risk.show', $id)
            ->with('success', "Data parameter triwulan {$request->quarter} berhasil ditambahkan.");
    }

    public function destroyPeriod(string $id, string $pivotId): RedirectResponse
    {
        DB::table('dep_monitoring_periods')->where('id', $pivotId)->delete();

        return redirect()->route('department-risk.show', $id)
            ->with('success', 'Data riwayat triwulan berhasil dihapus.');
    }
}
