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

        // 1. LOGIKA UNTUK TAB DASHBOARD=
        if ($tab === 'dashboard') {
            // Default 'all' agar saat pertama kali masuk menampilkan Semua Triwulan
            $selectedPeriode = $request->query('periode', 'all');
            $selectedYear = (int) $request->query('tahun', date('Y'));

            $quarterMap = [1 => 'TW1', 2 => 'TW2', 3 => 'TW3', 4 => 'TW4'];
            $twString = $quarterMap[$selectedPeriode] ?? null;

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
                ->where('dep_monitoring_periods.year', $selectedYear);

            // Filter Triwulan (jika bukan 'all')
            if ($selectedPeriode !== 'all' && $twString) {
                $baseDashboardQuery->where('dep_monitoring_periods.quarter', $twString);
            }

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

            $trendData = [
                ($risksPerTrend['Naik'] ?? $risksPerTrend['naik'] ?? 0),
                ($risksPerTrend['Turun'] ?? $risksPerTrend['turun'] ?? 0),
                (($risksPerTrend['Stagnan'] ?? 0) + ($risksPerTrend['stagnan'] ?? 0) + ($risksPerTrend['Stabil'] ?? 0) + ($risksPerTrend['stabil'] ?? 0)),
            ];

            // LOGIKA PEMBUATAN DATASETS UNTUK GRAFIK MATRIX (DIKEMBALIKAN)
            $stackedTemplates = [];
            $colorMapping = [
                'High'             => '#ef4444', // Merah
                'Moderate to High' => '#f59e0b', // Kuning/Amber
                'Moderate'         => '#eab308', // Kuning terang
                'Low to Moderate'  => '#3b82f6', // Biru
                'Low'              => '#10b981'  // Hijau
            ];

            // Inisialisasi kerangka warna untuk tiap level
            foreach ($allLevels as $level) {
                $levelName = $level->nama_level ?? $level->level;
                $stackedTemplates[$level->id_level] = [
                    'label' => $levelName,
                    'backgroundColor' => $colorMapping[$levelName] ?? '#cbd5e1',
                    'data' => []
                ];
            }

            // Loop per Departemen
            foreach ($allUnits as $unit) {
                $totalUnitRisks = $risksPerDept[$unit->id_unit] ?? 0;

                // 1. Array ini khusus untuk grafik pertama (warna biru tunggal)
                $labels[] = $unit->nama_unit;
                $data[] = $totalUnitRisks;

                // 2. Query ini khusus untuk memecah data level pada grafik kedua (Matrix)
                $currentDeptRisks = [];
                if ($totalUnitRisks > 0) {
                    $currentDeptRisks = (clone $baseDashboardQuery)
                        ->where('dep_monitoring.id_unit', $unit->id_unit)
                        ->selectRaw('dep_monitoring_periods.id_level, count(*) as total')
                        ->groupBy('dep_monitoring_periods.id_level')
                        ->pluck('total', 'id_level')
                        ->toArray();
                }

                foreach ($allLevels as $level) {
                    $stackedTemplates[$level->id_level]['data'][] = $currentDeptRisks[$level->id_level] ?? 0;
                }
            }

            // Hasil akhir array untuk grafik horizontal Matrix
            $chartDatasets = array_values($stackedTemplates);
            // -------------------------------------------------------------

            // Distribusi Bar Persentase Level
            foreach ($allLevels as $level) {
                $count = $risksPerLevel[$level->id_level] ?? 0;
                $maxLevelCount = max($maxLevelCount, $count);
                $levelDistributionData[] = ['name' => $level->nama_level ?? $level->level, 'count' => $count];
            }

            foreach ($levelDistributionData as &$item) {
                $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
            }

            // Kategori Departemen
            foreach ($allCategories as $category) {
                $catLabels[] = $category->nama_kategori;
                $catData[] = $risksPerCategory[$category->id_kategori] ?? 0;
            }

            $periodDisplay = $selectedPeriode === 'all' ? "Semua Triwulan - {$selectedYear}" : "Triwulan {$selectedPeriode} - {$selectedYear}";

            $dashboardData = [
                'summary' => [
                    'total_risiko'      => $totalRisiko,
                    'risiko_aktif'      => $risikoAktif,
                    'jumlah_departemen' => $allUnits->count(),
                ],
                'period' => $periodDisplay,
                'level_distribution' => $levelDistributionData,
            ];

            return view('departemen.index', compact(
                'tab', 'selectedPeriode', 'selectedYear', 'dashboardData',
                'labels', 'data', 'chartDatasets', 'catLabels', 'catData',
                'trendLabels', 'trendData'
            ));
        }

        // 2. LOGIKA UNTUK TAB DATA (Tabel List)
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

        $validated['value']    = 0;
        $validated['inherent'] = 0;
        $validated['trend']    = 'Stabil';
        $validated['id_level'] = $defaultLevel ? $defaultLevel->id_level : 1;

        DepMonitoring::create($validated);

        return redirect()->route('department-risk.index', ['tab' => 'data'])->with('success', 'Risk Department berhasil ditambahkan.');
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

        return redirect()->route('department-risk.index', ['tab' => 'data'])->with('success', 'Risk Department berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $risk = DepMonitoring::findOrFail($id);
        $risk->delete();

        return redirect()->route('department-risk.index', ['tab' => 'data'])->with('success', 'Risk Department berhasil dihapus.');
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
            'quarter'                 => ['required', 'in:TW1,TW2,TW3,TW4'],
            'year'                    => ['required', 'integer', 'min:2020', 'max:2099'],
            'value'                   => ['required', 'numeric', 'min:1', 'max:25'],
            'inherent'                => ['required', 'numeric', 'min:1', 'max:25'],
            'target'                  => ['required', 'numeric', 'min:1', 'max:25'],
            'penanganan'              => ['required', 'in:Belum,Proses,Sudah'],
            'calculated_trend'        => ['required', 'string'],
            'calculated_level'        => ['required'],
            'calculated_target_level' => ['required'],
        ]);

        $risk = DepMonitoring::findOrFail($id);
        $reqYear = $request->year;
        $reqQuarter = $request->quarter;

        // 1. Cek Duplikasi Input
        $isExist = DB::table('dep_monitoring_periods')
            ->where('id_monitoring', $id)
            ->where('quarter', $reqQuarter)
            ->where('year', $reqYear)
            ->exists();

        if ($isExist) {
            return redirect()->back()->with('error', "Periode {$reqQuarter} Tahun {$reqYear} sudah terdaftar pada risiko ini.");
        }

        // 2. VALIDASI WAJIB URUT & AMBIL INHERENT SEBELUMNYA
        $inherentValue = $request->inherent;
        $quarterOrder = ['TW1' => 1, 'TW2' => 2, 'TW3' => 3, 'TW4' => 4];
        $currentQNum = $quarterOrder[$reqQuarter];

        // Jika bukan TW1, kita wajib cek kuartal sebelumnya
        if ($currentQNum > 1) {
            $prevQNum = $currentQNum - 1;
            $prevQ = array_search($prevQNum, $quarterOrder); // Hasilnya: TW1 / TW2 / TW3

            // Cari data kuartal sebelumnya di database
            $prevPeriod = DB::table('dep_monitoring_periods')
                ->where('id_monitoring', $id)
                ->where('year', $reqYear)
                ->where('quarter', $prevQ)
                ->first();

            // Jika kuartal sebelumnya tidak ditemukan, TOLAK!
            if (!$prevPeriod) {
                return redirect()->back()->with('error', "Gagal! Anda harus mengisi riwayat $prevQ Tahun $reqYear terlebih dahulu sebelum mengisi $reqQuarter.");
            }

            // Ganti Inherent secara paksa dari Current (Value) kuartal sebelumnya
            $inherentValue = $prevPeriod->value;
        }

        // 3. Kalkulasi Level
        $levelInput = $request->calculated_level;
        $levelRecord = LevelRisiko::where('id_level', $levelInput)->orWhere('nama_level', $levelInput)->first();
        $idLevelTerbaru = $levelRecord ? $levelRecord->id_level : $risk->id_level;

        $targetLevelInput = $request->calculated_target_level;
        $targetLevelRecord = LevelRisiko::where('id_level', $targetLevelInput)->orWhere('nama_level', $targetLevelInput)->first();
        $idLevelTarget = $targetLevelRecord ? $targetLevelRecord->id_level : 1;

        // 4. Simpan ke Pivot dan Update Tabel Utama
        $risk->periods()->attach($idLevelTerbaru, [
            'quarter'         => $reqQuarter,
            'year'            => $reqYear,
            'value'           => $request->value,
            'inherent'        => $inherentValue, // Menggunakan Inherent yang sudah diverifikasi
            'trend'           => $request->calculated_trend,
            'target_value'    => $request->target,
            'target_id_level' => $idLevelTarget,
            'penanganan'      => $request->penanganan,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $risk->update([
            'id_level'        => $idLevelTerbaru,
            'value'           => $request->value,
            'inherent'        => $inherentValue,
            'trend'           => $request->calculated_trend,
            'penanganan'      => $request->penanganan,
            'target_value'    => $request->target,
            'target_id_level' => $idLevelTarget,
        ]);

        return redirect()->route('department-risk.show', $id)
                        ->with('success', "Data parameter triwulan $reqQuarter berhasil ditambahkan.");
    }

    public function destroyPeriod(string $id, string $pivotId): RedirectResponse
    {
        DB::table('dep_monitoring_periods')->where('id', $pivotId)->delete();
        return redirect()->route('department-risk.show', $id)->with('success', 'Data riwayat triwulan berhasil dihapus.');
    }

    public function getChartData()
    {
        $data = DepMonitoring::with('levelRisiko')
            ->select('id_level', DB::raw('count(*) as total'))
            ->groupBy('id_level')
            ->get();

        // Format data untuk Chart.js
        return response()->json([
            'labels' => $data->pluck('levelRisiko.nama_level'),
            'values' => $data->pluck('total'),
            'colors' => ['#FF0000', '#F28C28', '#FFD700', '#90EE90', '#228B22'] // Sesuaikan dengan warna di gambar Anda
        ]);
}
}
