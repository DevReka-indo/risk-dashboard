<?php

namespace App\Http\Controllers;

use App\Models\TopUnitKerja;
use App\Models\DepMonitoring;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class DepartemenController extends Controller
{
    public function index(Request $request): View
    {
        $search     = $request->string('search')->toString();
        $unitId     = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId    = $request->string('level_id')->toString();
        $trend      = $request->string('trend')->toString();
        $type       = $request->string('type')->toString();
        $status     = $request->string('status')->toString();
        $periodId   = $request->string('period_id')->toString();

        // Tangkap input manual dari form filter tahun & triwulan
        $tahun      = $request->string('tahun')->toString();
        $triwulan   = $request->string('triwulan')->toString();
        $tab        = $request->string('tab')->toString() ?: 'data';

        // Logika pencarian id_period berdasarkan tahun & triwulan pilihan user
        if ($tahun || $triwulan) {
            $matchedPeriod = Period::query()
                ->when($tahun, fn($q) => $q->where('year', $tahun))
                ->when($triwulan, fn($q) => $q->where('quarter', $triwulan))
                ->first();

            if ($matchedPeriod) {
                $periodId = $matchedPeriod->id_period;
            } else {
                $periodId = 'none'; // Menghasilkan data kosong jika kombinasi tidak ditemukan
            }
        }

        // --- QUERY UTAMA UNTUK TABEL DATA ---
        $risks = DepMonitoring::query()
            ->with([
                'unitKerja',
                'kategoriRisiko',
                'levelRisiko',
                'periode' => fn($query) => $query->orderBy('year', 'desc')
                                                ->orderBy('quarter', 'desc')
            ])
            ->when($search, fn($q) => $q->where('risk_event_deta', 'like', "%{$search}%"))
            ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
            ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
            ->when($levelId, fn($q) => $q->where('id_level', $levelId))
            ->when($trend, fn($q) => $q->where('trend', $trend))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status !== '', fn($q) => $q->where('status', (bool) $status))
            ->oldest('id_monitoring')
            ->paginate(10)
            ->withQueryString();

        $units      = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels     = LevelRisiko::all();
        $periods    = Period::orderBy('year', 'desc')->orderBy('quarter', 'desc')->get();

        // Membuat rentang tahun dinamis dari 2024 hingga 3 tahun ke depan
        $currentYear = (int) date('Y');
        $availableYears = range(2024, $currentYear + 3);

        // --- DEKLARASI VARIABEL AWAL (Pencegah Error Merah / Undefined Variable) ---
        $chartLabels          = [];
        $chartData            = [];
        $levelDistribution    = collect([]);
        $categoryDistribution = collect([]);
        $statusDistribution   = collect([]);
        $trendRisks           = collect([]);
        $rekapUnitLevel       = collect([]);

        // Variabel untuk Grafik Tren Risiko Horizontal
        $trendLabels          = ['Naik', 'Turun', 'Stagnan'];
        $trendData            = [0, 0, 0];

        // Variabel untuk Grafik Stacked Matriks Level per Departemen
        $matrixChartLabels    = [];
        $matrixChartDatasets  = [];

        // --- PROSES AMBIL DATA FILTER PERIODE ---
        $filteredPeriodIds = [];
        if ($tahun || $triwulan) {
            $filteredPeriodIds = Period::query()
                ->when($tahun, fn($q) => $q->where('year', $tahun))
                ->when($triwulan, fn($q) => $q->where('quarter', $triwulan))
                ->pluck('id_period')
                ->toArray();
        }

        // --- HITUNG VALUE SUMMARY CARD SECARA AMAN ---
        $baseQuery = DepMonitoring::query()
            ->when(($tahun || $triwulan) && !empty($filteredPeriodIds), function($q) use ($filteredPeriodIds) {
                return $q->whereIn('id_period', $filteredPeriodIds);
            })
            ->when(($tahun || $triwulan) && empty($filteredPeriodIds), function($q) {
                return $q->whereNull('id_period');
            });

        $summary = [
            'total_risiko'    => (clone $baseQuery)->count(),
            'risiko_aktif'    => (clone $baseQuery)->where('status', true)->count(),
            'rata_rata_nilai' => 0,
            'tren'            => 'Stagnan'
        ];

        // Label dinamis untuk sub-header
        $periodLabel = ($triwulan && $tahun) ? "{$triwulan} - {$tahun}" : "Saat Ini";
        $period = [
            'label'          => $periodLabel,
            'previous_label' => 'Bulan Sebelumnya'
        ];

        // --- KALKULASI UTAMA DASHBOARD (Hanya Dieksekusi Saat Tab Dashboard Aktif) ---
        if ($tab === 'dashboard') {

            // 1. Hitung data untuk Grafik Distribusi Utama Per Departemen
            $riskCounts = DepMonitoring::query()
                ->selectRaw('id_unit, count(*) as total')
                ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
                ->when($levelId, fn($q) => $q->where('id_level', $levelId))
                ->when($trend, fn($q) => $q->where('trend', $trend))
                ->when($type, fn($q) => $q->where('type', $type))
                ->when($status !== '', fn($q) => $q->where('status', (bool) $status))
                ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                ->groupBy('id_unit')
                ->pluck('total', 'id_unit');

            foreach ($units as $unit) {
                if ($unitId && $unit->id_unit != $unitId) continue;

                $chartLabels[] = $unit->nama_unit;
                $chartData[]   = $riskCounts->get($unit->id_unit, 0);
            }

            // 2. Hitung data untuk Distribusi Level Risiko
            $levelDistribution = LevelRisiko::orderBy('id_level', 'asc')->get()->map(function($level) use ($filteredPeriodIds) {
                $count = DepMonitoring::query()->where('id_level', $level->id_level)
                    ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                    ->count();

                return [
                    'label' => $level->nama_level,
                    'total' => $count
                ];
            });

            // 3. Hitung data untuk Jumlah Kategori Risiko
            $categoryDistribution = KategoriRisiko::orderBy('nama_kategori', 'asc')->get()->map(function($category) use ($filteredPeriodIds) {
                $count = DepMonitoring::query()->where('id_kategori', $category->id_kategori)
                    ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                    ->count();

                return [
                    'label' => $category->nama_kategori,
                    'total' => $count
                ];
            });

            // 4. Hitung data untuk Trend Pergerakan Risiko (List Detail)
            $trendRisks = DepMonitoring::with(['unitKerja', 'levelRisiko', 'periode'])
                ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
                ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
                ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                ->get()
                ->map(function($risk, $index) {
                    $statusTrend = $risk->trend === 'Stabil' ? 'Stagnan' : ($risk->trend ?: 'Stagnan');

                    // AMAN: Ambil nilai pivot, jika null default ke 0
                    $currentValue = $risk->periode->pivot->value ?? 0;

                    // AMAN: Cek apakah relasi periode ada sebelum mengambil quarter & year
                    $periodLabel = $risk->periode
                        ? $risk->periode->quarter . ' ' . $risk->periode->year
                        : 'No Period';

                    return [
                        'id_monitoring'     => $risk->id_monitoring,
                        'number'            => $index + 1,
                        'risk_name'         => $risk->risk_event_deta,
                        'unit'              => $risk->unitKerja->nama_unit ?? 'N/A',
                        'level'             => $risk->levelRisiko->nama_level ?? 'N/A',
                        'trend'             => $statusTrend,
                        'current_value'     => $currentValue,
                        'trend_description' => $risk->risk_event_deta,
                        'trend_values'      => [
                            ['label' => $periodLabel, 'value' => (int)$currentValue]
                        ]
                    ];
                });

            // 5. Hitung Data Grafik Tren Risiko Horizontal (Naik, Turun, Stagnan)
            $trendCounts = DepMonitoring::query()
                ->selectRaw('trend, count(*) as total')
                ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                ->groupBy('trend')
                ->pluck('total', 'trend');

            $trendData = [
                (int)$trendCounts->get('Naik', 0),
                (int)$trendCounts->get('Turun', 0),
                (int)($trendCounts->get('Stagnan', 0) + $trendCounts->get('Stabil', 0) + $trendCounts->get('Fluktuatif', 0))
            ];

            // 6. Matriks Rincian & Grafik Bertumpuk Level Risiko per Unit Kerja
            $matrixData = [];
            $riskMatrixData = DepMonitoring::query()
                ->selectRaw('id_unit, id_level, count(*) as total')
                ->when(!empty($filteredPeriodIds), fn($q) => $q->whereIn('id_period', $filteredPeriodIds))
                ->groupBy('id_unit', 'id_level')
                ->get();

            foreach ($riskMatrixData as $data) {
                $matrixData[$data->id_unit][$data->id_level] = $data->total;
            }

            $rekapUnitLevel = $units->map(function($unit) use ($levels, $matrixData) {
                $row = [
                    'nama_unit'  => $unit->nama_unit,
                    'levels'     => [],
                    'total_unit' => 0
                ];

                foreach ($levels as $level) {
                    $count = $matrixData[$unit->id_unit][$level->id_level] ?? 0;
                    $row['levels'][$level->id_level] = $count;
                    $row['total_unit'] += $count;
                }

                return $row;
            });

            // Format Data Khusus untuk Grafik Stacked Bar Chart.js
            $matrixChartLabels = $units->pluck('nama_unit')->toArray();

            $levelColors = [
                1 => '#ef4444', // Red
                2 => '#f97316', // Orange
                3 => '#eab308', // Yellow
                4 => '#3b82f6', // Blue
                5 => '#10b981', // Emerald
            ];

            foreach ($levels as $level) {
                $dataPoints = [];
                foreach ($units as $unit) {
                    $dataPoints[] = $matrixData[$unit->id_unit][$level->id_level] ?? 0;
                }

                $matrixChartDatasets[] = [
                    'label'           => $level->nama_level,
                    'data'            => $dataPoints,
                    'backgroundColor' => $levelColors[$level->id_level] ?? '#64748b',
                    'borderRadius'    => 4
                ];
            }
        }

        return view('departemen.index', compact(
            'risks', 'search', 'unitId', 'categoryId', 'levelId', 'trend',
            'type', 'status', 'periodId', 'tahun', 'triwulan', 'availableYears', 'units', 'categories', 'levels',
            'periods', 'tab', 'chartLabels', 'chartData', 'summary', 'period',
            'levelDistribution', 'categoryDistribution', 'statusDistribution',
            'trendRisks', 'trendLabels', 'trendData', 'rekapUnitLevel', 'matrixChartLabels', 'matrixChartDatasets'
        ));
    }

    public function create(): View
    {
        $units      = TopUnitKerja::all();
        $categories = KategoriRisiko::all();
        $levels     = LevelRisiko::all();
        $periods    = Period::orderBy('year', 'desc')->orderBy('quarter', 'desc')->get();

        return view('departemen.create', compact('units', 'categories', 'levels', 'periods'));
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

        $validated['value']    = 0;
        $validated['inherent'] = 0;
        $validated['trend']    = 'Stabil';
        $validated['id_level'] = $defaultLevel ? $defaultLevel->id_level : 1;

        DepMonitoring::create($validated);

        return redirect()
            ->route('department-risk.index')
            ->with('success', 'Risk Department berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $risk       = DepMonitoring::findOrFail($id);
        $units      = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels     = LevelRisiko::all();
        $periods    = Period::orderBy('year', 'desc')->orderBy('quarter', 'desc')->get();

        return view('departemen.edit', compact('risk', 'units', 'categories', 'levels', 'periods'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
            'id_period'       => ['nullable', 'integer', 'exists:periods,id_period'],
        ]);

        $risk = DepMonitoring::findOrFail($id);
        $risk->update($validated);

        return redirect()
            ->route('department-risk.index')
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
        $risk   = DepMonitoring::with(['unitKerja', 'kategoriRisiko', 'periods'])->findOrFail($id);
        $levels = DB::table('level_risiko')->orderBy('urutan', 'asc')->get();

        return view('departemen.show', compact('risk', 'levels'));
    }

    public function updatePeriod(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'quarter'  => ['required', 'in:TW1,TW2,TW3,TW4'],
            'year'     => ['required', 'integer', 'min:2020', 'max:2099'],
            'value'    => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'id_level' => ['required', 'integer', 'exists:level_risiko,id_level'],
            'trend'    => ['required', 'in:Naik,Turun,Stabil'],
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

        $risk->periods()->attach($request->id_level, [
            'quarter'    => $request->quarter,
            'year'       => $request->year,
            'value'      => $request->value,
            'inherent'   => $request->inherent,
            'trend'      => $request->trend,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('department-risk.show', $id)
            ->with('success', 'Data parameter triwulan baru berhasil ditambahkan.');
    }

    public function destroyPeriod(string $id, string $pivotId): RedirectResponse
    {
        DB::table('dep_monitoring_periods')->where('id', $pivotId)->delete();

        return redirect()->route('department-risk.show', $id)
            ->with('success', 'Data parameter triwulan berhasil dihapus.');
    }
}
