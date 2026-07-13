<?php

namespace App\Http\Controllers;

use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\SmapMonitoring;
use App\Models\TopUnitKerja;
use App\Models\Period;
use App\Models\SmapMonitoringPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SmapController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'list');

        if ($tab === 'dashboard') {
            return $this->getDashboardView($request, $tab);
        }

        return $this->getListView($request, $tab);
    }

    /**
     * Logika Khusus untuk Tab Dashboard (Grafik & Ringkasan)
     */
    protected function getDashboardView(Request $request, string $tab): View
    {
        $defaultQuarter = ceil(date('n') / 3);
        $selectedPeriode = (int) $request->query('periode', $defaultQuarter);
        $selectedYear = (int) $request->query('tahun', date('Y'));

        // Mencari record di master periode (tetap dicari jika diperlukan datanya)
        $period = Period::query()
            ->where('quarter', $selectedPeriode)
            ->where('year', $selectedYear)
            ->first();

        $totalRisiko = 0;
        $risikoAktif = 0;

        $labels = [];
        $data = [];
        $catLabels = [];
        $catData = [];

        $risksPerDept = [];
        $risksPerLevel = [];
        $risksPerCategory = [];
        $levelDistributionData = [];
        $maxLevelCount = 0;

        $allUnits = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $allLevels = LevelRisiko::orderBy('id_level', 'asc')->get();
        $allCategories = KategoriRisiko::query()
            ->where('type', 'smap')
            ->orderBy('nama_kategori', 'asc')
            ->get();

        $trendLabels = ['Naik', 'Turun', 'Stagnan'];
        $trendData = [0, 0, 0];

        $chartDatasets = [];

        // Definisikan di luar IF agar Intelephense aman dari redline undefined variable
        $stringQuarter = 'Q' . $selectedPeriode;

        if ($period) {
            // 1. HITUNG TOTAL RISIKO
            $totalRisiko = \App\Models\SmapMonitoringPeriod::query()
                ->where('quarter', $stringQuarter)
                ->where('year', $selectedYear)
                ->count();

            // 2. HITUNG RISIKO AKTIF
            $risikoAktif = DB::table('smap_monitoring_periods')
                ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
                ->where('smap_monitoring_periods.quarter', $stringQuarter)
                ->where('smap_monitoring_periods.year', $selectedYear)
                ->where('smap_monitoring.status', 1)
                ->count();

            // 🔥 3. HITUNG JUMLAH RISIKO PER DEPARTEMEN (Kunci Utama agar Grafik Muncul)
            $risksPerDept = DB::table('smap_monitoring_periods')
                ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
                ->where('smap_monitoring_periods.quarter', $stringQuarter)
                ->where('smap_monitoring_periods.year', $selectedYear)
                ->selectRaw('smap_monitoring.id_unit, count(*) as total')
                ->groupBy('smap_monitoring.id_unit')
                ->pluck('total', 'id_unit')
                ->toArray();

            // 4. HITUNG JUMLAH RISIKO PER LEVEL
            $risksPerLevel = \App\Models\SmapMonitoringPeriod::query()
                ->where('quarter', $stringQuarter)
                ->where('year', $selectedYear)
                ->selectRaw('id_level, count(*) as total')
                ->groupBy('id_level')
                ->pluck('total', 'id_level')
                ->toArray();

            // 🔥 5. HITUNG JUMLAH RISIKO PER KATEGORI (Kunci Utama Grafik Kategori)
            $risksPerCategory = DB::table('smap_monitoring_periods')
                ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
                ->where('smap_monitoring_periods.quarter', $stringQuarter)
                ->where('smap_monitoring_periods.year', $selectedYear)
                ->selectRaw('smap_monitoring.id_kategori, count(*) as total')
                ->groupBy('smap_monitoring.id_kategori')
                ->pluck('total', 'id_kategori')
                ->toArray();

            // 6. HITUNG TREND PERUBAHAN
            $risksPerTrend = \App\Models\SmapMonitoringPeriod::query()
                ->where('quarter', $stringQuarter)
                ->where('year', $selectedYear)
                ->selectRaw('trend, count(*) as total')
                ->groupBy('trend')
                ->pluck('total', 'trend')
                ->toArray();

            $trendData = [
                (int)($risksPerTrend['naik'] ?? $risksPerTrend['Naik'] ?? 0),
                (int)($risksPerTrend['turun'] ?? $risksPerTrend['Turun'] ?? 0),
                (int)($risksPerTrend['stabil'] ?? $risksPerTrend['Stabil'] ?? $risksPerTrend['stagnan'] ?? $risksPerTrend['Stagnan'] ?? 0),
            ];
        }

        $stackedTemplates = [];
        $colorMapping = [
            'High'             => '#FF0100',
            'Moderate to High' => '#FFC000',
            'Moderate'         => '#FFFF00',
            'Low to Moderate'  => '#91D050',
            'Low'              => '#03B050'
        ];

        foreach ($allLevels as $level) {
            $stackedTemplates[$level->id_level] = [
                'label' => $level->nama_level,
                'backgroundColor' => $colorMapping[$level->nama_level] ?? '#cbd5e1',
                'data' => []
            ];
        }

        foreach ($allUnits as $unit) {
            $totalUnitRisks = $risksPerDept[$unit->id_unit] ?? 0;

            if ($totalUnitRisks > 0) {
                $data[] = $totalUnitRisks;
                $labels[] = $unit->nama_unit;

                $currentDeptRisks = [];
                if ($period) {
                    $currentDeptRisks = DB::table('smap_monitoring_periods')
                        ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
                        ->where('smap_monitoring_periods.quarter', $stringQuarter)
                        ->where('smap_monitoring_periods.year', $selectedYear)
                        ->where('smap_monitoring.id_unit', $unit->id_unit)
                        ->selectRaw('smap_monitoring_periods.id_level, count(*) as total')
                        ->groupBy('smap_monitoring_periods.id_level')
                        ->pluck('total', 'id_level')
                        ->toArray();
                }

                foreach ($allLevels as $level) {
                    $stackedTemplates[$level->id_level]['data'][] = (int)($currentDeptRisks[$level->id_level] ?? 0);
                }
            }
        }

        $data = array_values($data);
        $labels = array_values($labels);
        $chartDatasets = array_values($stackedTemplates);

        foreach ($allLevels as $level) {
            $count = $risksPerLevel[$level->id_level] ?? 0;
            $maxLevelCount = max($maxLevelCount, $count);

            $levelDistributionData[] = [
                'name'  => $level->nama_level,
                'count' => $count
            ];
        }
        foreach ($levelDistributionData as &$item) {
            $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
        }

        foreach ($allCategories as $category) {
            $catLabels[] = $category->nama_kategori;
            $catData[] = $risksPerCategory[$category->id_kategori] ?? 0;
        }
        $catLabels = array_values($catLabels);
        $catData = array_values($catData);

        $jumlahDepartemen = $allUnits->count();

        $dashboardData = [
            'summary' => [
                'total_risiko'      => $totalRisiko,
                'risiko_aktif'      => $risikoAktif,
                'jumlah_departemen' => $jumlahDepartemen,
            ],
            'period' => "Triwulan {$selectedPeriode} - {$selectedYear}",
            'heatmap' => [],
            'level_distribution' => $levelDistributionData,
            'trend_risk' => $trendData,
            'category_distribution' => $catData,
            'status_distribution' => [],
        ];

        return view('smap.index', compact(
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

    /**
     * Logika Khusus untuk Tab List (Tabel, Pencarian, & Filter)
     */
    protected function getListView(Request $request, string $tab): View
    {
        $search = $request->string('search')->toString();
        $unitId = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId = $request->string('level_id')->toString();
        $trend = $request->string('trend')->toString();
        $status = $request->string('status')->toString();

        $smapRisks = SmapMonitoring::query()
            ->where('parent_id', null)
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'latestPeriode.period'])
            ->when($search, function ($query) use ($search): void {
                $query->where('risk_event_deta', 'like', "%{$search}%");
            })
            ->when($unitId, function ($query) use ($unitId): void {
                $query->where('id_unit', $unitId);
            })
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->where('id_kategori', $categoryId);
            })
            ->when($levelId, function ($query) use ($levelId): void {
                $query->where('id_level', $levelId);
            })
            ->when($trend, function ($query) use ($trend): void {
                $query->where('trend', $trend);
            })
            ->when($status !== '', function ($query) use ($status): void {
                $query->where('status', (bool) $status);
            })
            ->oldest('id_smap')
            ->paginate(10)
            ->withQueryString();

        $units = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels = LevelRisiko::all();

        return view('smap.index', compact(
            'tab',
            'smapRisks',
            'search',
            'unitId',
            'categoryId',
            'levelId',
            'trend',
            'status',
            'units',
            'categories',
            'levels',
        ));
    }

    public function create(): View
    {
        $units = TopUnitKerja::all();
        $categories = KategoriRisiko::whereType('smap')->get();
        $levels = LevelRisiko::all();

        return view('smap.create', compact('units', 'categories', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'created_at'      => ['required', 'date'],
        ]);

        $validated['parent_id']  = null;
        $validated['id_period']  = null;
        $validated['id_level']   = 1;
        $validated['value']      = 0;
        $validated['inherent']   = 0;
        $validated['trend']      = 'Stabil';

        SmapMonitoring::create($validated);

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $risk = SmapMonitoring::findOrFail($id);

        $units = TopUnitKerja::orderBy('nama_unit', 'asc')->get();

        $categories = KategoriRisiko::query()
            ->where('type', 'smap')
            ->orderBy('nama_kategori', 'asc')
            ->get();

        return view('smap.edit', compact('risk', 'units', 'categories'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
        ]);

        $risk = SmapMonitoring::findOrFail($id);

        $validated['status'] = $request->boolean('status');

        $risk->update($validated);

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $risk = SmapMonitoring::findOrFail($id);
        $risk->delete();

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil dihapus.');
    }

    public function show(string $id): View
{
    // 1. Ambil data SMAP beserta relasi periodenya
    $risk = SmapMonitoring::with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'detailPeriode.period'])->findOrFail($id);

    // 2. Ambil data master period
    $periods = Period::orderBy('year', 'desc')->orderBy('quarter', 'asc')->get();

    // 3. 🔥 Bentuk data riwayat agar memiliki properti ['value']
    $historyData = [];
    if ($risk->detailPeriode) {
        foreach ($risk->detailPeriode as $history) {
            $qKey = $history->quarter;

            // Konversi key jika di DB berupa Q1/Q2 atau angka langsung
            if (str_contains($qKey, 'Q')) {
                $qKey = str_replace('Q', 'TW', $qKey);
            } elseif (is_numeric($qKey)) {
                $qKey = 'TW' . $qKey;
            }

            // Disimpan sebagai objek array yang menampung 'value'
            $historyData[$history->year][$qKey] = [
                'value' => (int) $history->value,
            ];
        }
    }

    // 4. Kirim historyData ke view
    return view('smap.show', compact('risk', 'periods', 'historyData'));
}

    public function storeMonitoring(Request $request, $id)
    {
        $request->validate([
            'quarter'                 => 'required|in:TW1,TW2,TW3,TW4',
            'year'                    => 'required|numeric|min:2020|max:2099',
            'value'                   => 'required|numeric|min:1|max:25',
            'inherent'                => 'required|numeric',
            'status_monitoring'       => 'required|in:0,1',
            'calculated_level'        => 'required|integer|min:1|max:5',

            'inherent_target'         => 'required|numeric|min:1|max:25',
            'calculated_level_target' => 'required|integer|min:1|max:5',
        ]);

        // 🔥 Balik pemetaannya karena inputan yang masuk sekarang berbentuk 'TW'
        $quarterMapping = [
            'TW1' => ['numeric' => '1', 'text' => 'TW1'],
            'TW2' => ['numeric' => '2', 'text' => 'TW2'],
            'TW3' => ['numeric' => '3', 'text' => 'TW3'],
            'TW4' => ['numeric' => '4', 'text' => 'TW4'],
        ];

        $selectedQuarter = $quarterMapping[$request->quarter]['numeric'];
        $quarterText     = $quarterMapping[$request->quarter]['text'];
        $periodName      = $quarterText . ' ' . $request->year;

        $period = Period::firstOrCreate(
            ['period_name' => $periodName],
            [
                'year'    => $request->year,
                'quarter' => $selectedQuarter,
            ]
        );

        $parentRisk = SmapMonitoring::findOrFail($id);

        $exists = \App\Models\SmapMonitoringPeriod::query()
            ->where('id_smap', '=', (int) $parentRisk->id_smap)
            ->where('quarter', '=', $request->quarter)
            ->where('year', '=', $request->year)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'quarter' => "Monitoring untuk periode {$periodName} sudah pernah diinput."
                ]);
        }

        $idLevelTerbaru = (int) $request->calculated_level ?: $parentRisk->id_level;
        $idLevelTarget = (int) $request->calculated_level_target ?: null;

        \App\Models\SmapMonitoringPeriod::create([
            'id_smap'          => $parentRisk->id_smap,
            'quarter'          => $request->quarter,
            'year'             => $request->year,
            'id_level'         => $idLevelTerbaru,
            'id_level_target'  => $idLevelTarget,
            'value'            => $request->value,
            'inherent'         => $request->inherent,
            'inherent_target'  => $request->inherent_target,
            'trend'            => $request->calculated_trend,
        ]);

        $parentRisk->update([
            'status' => $request->status_monitoring
        ]);

        return redirect()->back()
            ->with('success', "Berhasil merekam perkembangan & target risiko untuk periode {$periodName}!");
    }

    public function destroyMonitoring(int $id_period): RedirectResponse
    {
        $monitoring = \App\Models\SmapMonitoringPeriod::findOrFail($id_period);
        $idSmapParent = $monitoring->id_smap;
        $monitoring->delete();

        return redirect()
            ->route('smap-risk.show', $idSmapParent)
            ->with('success', 'Riwayat monitoring berhasil dihapus.');
    }
}
