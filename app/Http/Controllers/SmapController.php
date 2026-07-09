<?php

namespace App\Http\Controllers;

use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\SmapMonitoring;
use App\Models\TopUnitKerja;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmapController extends Controller
{
   public function index(Request $request): \Illuminate\View\View
    {
        $tab = $request->query('tab', 'list');

        // ==========================================
        // 1. LOGIKA UNTUK TAB DASHBOARD
        // ==========================================
        if ($tab === 'dashboard') {
            $defaultQuarter = ceil(date('n') / 3);
            $selectedPeriode = (int) $request->query('periode', $defaultQuarter);
            $selectedYear = (int) $request->query('tahun', date('Y'));

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

            $trendLabels = ['Naik', 'Stabil', 'Turun'];
            $trendData = [0, 0, 0];


            if ($period) {
                // Kunci ID period-nya ke variabel biasa
                $periodId = $period->id_period;

                // 🔥 Mengubah whereNotNull menjadi where('...', '!=', null) biar Intelephense gak salah baca argumen
                $totalRisiko = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->count();

                $risikoAktif = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->where('status', 1)
                    ->count();

                $risksPerDept = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->selectRaw('id_unit, count(*) as total')
                    ->groupBy('id_unit')
                    ->pluck('total', 'id_unit')
                    ->toArray();

                $risksPerLevel = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->selectRaw('id_level, count(*) as total')
                    ->groupBy('id_level')
                    ->pluck('total', 'id_level')
                    ->toArray();

                $risksPerCategory = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->selectRaw('id_kategori, count(*) as total')
                    ->groupBy('id_kategori')
                    ->pluck('total', 'id_kategori')
                    ->toArray();

                $risksPerTrend = SmapMonitoring::query()
                    ->where('parent_id', '!=', null)
                    ->where('id_period', $periodId)
                    ->selectRaw('trend, count(*) as total')
                    ->groupBy('trend')
                    ->pluck('total', 'trend')
                    ->toArray();

                $trendData = [
                    ($risksPerTrend['naik'] ?? $risksPerTrend['Naik'] ?? 0),
                    ($risksPerTrend['turun'] ?? $risksPerTrend['Turun'] ?? 0),
                    ($risksPerTrend['stagnan'] ?? $risksPerTrend['Stagnan'] ?? 0),
                ];
            }

            foreach ($allUnits as $unit) {
                $labels[] = $unit->nama_unit;
                $data[] = $risksPerDept[$unit->id_unit] ?? 0;
            }

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
                'trend_risk' => [],
                'category_distribution' => [],
                'status_distribution' => [],
            ];

            return view('smap.index', compact(
                'tab',
                'selectedPeriode',
                'selectedYear',
                'dashboardData',
                'labels',
                'data',
                'catLabels',
                'catData',
                'trendLabels', // Dikirim ke blade
                'trendData'    // Dikirim ke blade
            ));
        }

        // ==========================================
        // 2. LOGIKA UNTUK TAB LIST (Tabel Data)
        // ==========================================
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

        // 3. Simpan data ke database
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
        $risk = SmapMonitoring::with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'detailPeriode.period'])->findOrFail($id);

        $periods = Period::orderBy('year', 'desc')->orderBy('quarter', 'asc')->get();

        return view('smap.show', compact('risk', 'periods'));
    }

public function storeMonitoring(Request $request, $id)
    {
        $request->validate([
            'quarter'           => 'required|in:Q1,Q2,Q3,Q4',
            'year'              => 'required|numeric|min:2020|max:2099',
            'value'             => 'required|numeric|min:1|max:25',
            'inherent'          => 'required|numeric',
            'status_monitoring' => 'required|in:0,1',
            'calculated_level'  => 'required|string', // Validasi tambahan dari form blade
        ]);

        $quarterMapping = [
            'Q1' => ['numeric' => '1', 'text' => 'TW1'],
            'Q2' => ['numeric' => '2', 'text' => 'TW2'],
            'Q3' => ['numeric' => '3', 'text' => 'TW3'],
            'Q4' => ['numeric' => '4', 'text' => 'TW4'],
        ];

        $selectedQuarter = $quarterMapping[$request->quarter]['numeric'];
        $quarterText     = $quarterMapping[$request->quarter]['text'];

        $periodName = $quarterText . ' ' . $request->year;

        $period = Period::firstOrCreate(
            ['period_name' => $periodName],
            [
                'year'    => $request->year,
                'quarter' => $selectedQuarter,
            ]
        );

        $parentRisk = SmapMonitoring::findOrFail($id);

        $exists = SmapMonitoring::query()
            ->where('parent_id', '=', (int) $parentRisk->id_smap)
            ->where('id_period', '=', (int) $period->id_period)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'quarter' => "Monitoring untuk periode {$periodName} sudah pernah diinput."
                ]);
        }

        // 🔥 PERBAIKAN 1: Konversi string calculated_level (Low, Medium, High, Significant)
        // dari form menjadi ID integer yang sesuai dengan isi tabel level_risiko Anda
        $levelMapping = [
            'Low'         => 1,
            'Medium'      => 2,
            'High'        => 3,
            'Significant' => 4,
        ];
        $idLevelTerbaru = $levelMapping[$request->calculated_level] ?? $parentRisk->id_level;

        // Simpan data riwayat perkembangan kuartal
        SmapMonitoring::create([
            'parent_id'       => $parentRisk->id_smap,
            'id_period'       => $period->id_period,
            'id_unit'         => $parentRisk->id_unit,
            'id_kategori'     => $parentRisk->id_kategori,
            'id_level'        => $idLevelTerbaru, // Menggunakan ID Level terhitung terbaru
            'risk_event_deta' => $parentRisk->risk_event_deta,
            'inherent'        => $request->inherent,
            'trend'           => $request->calculated_trend,
            'value'           => $request->value,
            'status'          => $request->status_monitoring,
        ]);

        // 🔥 PERBAIKAN 2: Update status Data Master (Parent) supaya halaman Index ikut ter-update otomatis
        $parentRisk->update([
            'status' => $request->status_monitoring
        ]);

        return redirect()->back()
            ->with('success', "Berhasil merekam perkembangan risiko untuk periode {$periodName}!");
    }

    public function destroyMonitoring(int $id_period): RedirectResponse
    {
        $monitoring = SmapMonitoring::query()
            ->where('id_period', $id_period)
            ->firstOrFail();

        $idSmap = $monitoring->parent_id;

        SmapMonitoring::destroy($monitoring->id_smap);

        return redirect()
            ->route('smap-risk.show', $idSmap)
            ->with('success', 'Riwayat monitoring berhasil dihapus.');
    }
}
