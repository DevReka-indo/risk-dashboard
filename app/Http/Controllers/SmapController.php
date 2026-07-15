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

        $selectedYear = $request->query('tahun');
        if (!$selectedYear) {
            $latestData = \App\Models\SmapMonitoringPeriod::latest('year')->first();
            $selectedYear = $latestData ? (int)$latestData->year : (int)date('Y');
        } else {
            $selectedYear = (int)$selectedYear;
        }

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

        $stringQuarter = 'TW' . $selectedPeriode;
        $quarterLookups = [$stringQuarter, (int)$selectedPeriode, (string)$selectedPeriode, 'Q' . $selectedPeriode];

        // 1. HITUNG TOTAL RISIKO
        $totalRisiko = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->count();

        // 2. HITUNG RISIKO AKTIF
        $risikoAktif = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->where('smap_monitoring.status', 1)
            ->count();

        // 3. HITUNG JUMLAH RISIKO PER DEPARTEMEN
        $risksPerDept = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('smap_monitoring.id_unit, count(*) as total')
            ->groupBy('smap_monitoring.id_unit')
            ->pluck('total', 'id_unit')
            ->toArray();

        // 4. HITUNG JUMLAH RISIKO PER LEVEL
        $risksPerLevel = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('id_level, count(*) as total')
            ->groupBy('id_level')
            ->pluck('total', 'id_level')
            ->toArray();

        // 5. HITUNG JUMLAH RISIKO PER KATEGORI
        $risksPerCategory = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('smap_monitoring.id_kategori, count(*) as total')
            ->groupBy('smap_monitoring.id_kategori')
            ->pluck('total', 'id_kategori')
            ->toArray();

        // 6. HITUNG TREND PERUBAHAN
        $risksPerTrend = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
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

        // LOGIKA PENYUSUNAN DATASET GRAFIK STACKED DEPARTEMEN
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

                $currentDeptRisks = DB::table('smap_monitoring_periods')
                    ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
                    ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
                    ->where('year', $selectedYear)
                    ->where('smap_monitoring.id_unit', $unit->id_unit)
                    ->selectRaw('smap_monitoring_periods.id_level, count(*) as total')
                    ->groupBy('smap_monitoring_periods.id_level')
                    ->pluck('total', 'id_level')
                    ->toArray();

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

        // PREPARASI STRUKTUR LEVEL KOSONG (1-5)
        $baseArray = array_fill_keys($allLevels->pluck('id_level')->toArray(), 0);

        // --- 🔥 FIXED BASELINE: PIE 1 - INHERENT MASTER (Mengunci data tahun ini dari master induk) ---
        $pieInherent = $baseArray;
        $inherentMasterData = DB::table('smap_monitoring')
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('id_level, count(*) as total')
            ->groupBy('id_level')
            ->pluck('total', 'id_level')
            ->toArray();
        foreach ($inherentMasterData as $lvl => $tot) { if($lvl) $pieInherent[$lvl] = (int)$tot; }

        // --- 🔥 DYNAMIC FILTER: PIE 2 - CURRENT RISK (Berubah dinamis mengikuti triwulan berjalan) ---
        $pieCurrent = $baseArray;
        foreach ($risksPerLevel as $lvl => $tot) { if($lvl) $pieCurrent[$lvl] = (int)$tot; }

        // --- 🔥 FIXED TARGET: PIE 3 - TARGET MASTER (Mengunci data tahun ini dari master induk) ---
        $pieTarget = $baseArray;
        $targetMasterData = DB::table('smap_monitoring')
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('id_level_target, count(*) as total')
            ->groupBy('id_level_target')
            ->pluck('total', 'id_level_target')
            ->toArray();
        foreach ($targetMasterData as $lvl => $tot) { if($lvl) $pieTarget[$lvl] = (int)$tot; }

        // --- 📊 PIE 4: PROGRES PENANAMAN RISIKO (Triwulan Berjalan) ---
        $baseProgres = ['belum' => 0, 'proses' => 0, 'selesai' => 0];
        $progresOffData = DB::table('smap_monitoring_periods')
            ->where('year', $selectedYear)
            ->whereIn('quarter', $quarterLookups)
            ->selectRaw('status_penanganan, count(*) as total')
            ->groupBy('status_penanganan')
            ->pluck('total', 'status_penanganan')
            ->toArray();
        $pieProgresOff = $baseProgres;
        foreach ($progresOffData as $status => $tot) {
            $key = strtolower($status ?: 'belum');
            if (array_key_exists($key, $pieProgresOff)) { $pieProgresOff[$key] = (int)$tot; }
        }

        // --- 📊 PIE 5: EFEKTIVITAS MITIGASI RISIKO (Triwulan Berjalan) ---
        $baseEfektif = [
            'Pencatatan'          => 0,
            'Effective'           => 0,
            'Mostly Effective'    => 0,
            'Partially Effective' => 0,
            'In-Effective'        => 0,
            'Unmeasurable'        => 0
        ];
        $efektifOffData = DB::table('smap_monitoring_periods')
            ->where('year', $selectedYear)
            ->whereIn('quarter', $quarterLookups)
            ->selectRaw('efektif_risiko, count(*) as total')
            ->groupBy('efektif_risiko')
            ->pluck('total', 'efektif_risiko')
            ->toArray();
        $pieEfektifOff = $baseEfektif;
        foreach ($efektifOffData as $status => $tot) {
            if ($status && array_key_exists($status, $pieEfektifOff)) { $pieEfektifOff[$status] = (int)$tot; }
        }

        // KEMAS SEMUA STRUKTUR KE VARIABLE UTAMA YANG DIBACA OLEH JAVASCRIPT BLADE
        $smapPieData = [
            'labels'   => array_values($allLevels->pluck('nama_level')->toArray()),
            'inherent' => array_values($pieInherent),
            'current'  => array_values($pieCurrent),
            'target'   => array_values($pieTarget),
            'progres'  => [
                'labels' => ['Belum Dimulai', 'Sedang Berjalan', 'Selesai'],
                'off'    => array_values($pieProgresOff)
            ],
            'efektif'  => [
                'labels' => array_keys($baseEfektif),
                'off'    => array_values($pieEfektifOff)
            ]
        ];

        $summary = $dashboardData['summary'];
        $periodText = $dashboardData['period'];

        return view('smap.index', compact(
            'tab', 'selectedPeriode', 'selectedYear', 'dashboardData', 'summary', 'periodText',
            'labels', 'data', 'chartDatasets', 'catLabels', 'catData', 'trendLabels', 'trendData', 'smapPieData'
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
            'tab', 'smapRisks', 'search', 'unitId', 'categoryId', 'levelId', 'trend', 'status', 'units', 'categories', 'levels',
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
        // 1. Tambahkan validasi untuk 4 field baseline baru
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'created_at'      => ['required', 'date'],
            // Validasi tambahan
            'inherent'        => ['required', 'integer', 'min:1', 'max:25'],
            'id_level'        => ['required', 'integer', 'exists:level_risiko,id_level'],
            'inherent_target' => ['required', 'integer', 'min:1', 'max:25'],
            'id_level_target' => ['required', 'integer', 'exists:level_risiko,id_level'],
        ]);

        $validated['parent_id']  = null;
        $validated['id_period']  = null;
        $validated['value']      = 0;
        $validated['trend']      = 'Stabil';

        // 2. Simpan record master ke database
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
        // 1. Validasi semua data yang dikirimkan oleh form edit (Kurung penutup yang hilang sudah diperbaiki)
        $validated = $request->validate([
            'risk_event_deta'   => ['required', 'string'],
            'id_kategori'       => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'created_at'        => ['required', 'date'],
            'inherent'          => ['required', 'integer', 'between:1,25'],
            'inherent_target'   => ['required', 'integer', 'between:1,25'],
            'status'            => ['required', 'string', 'in:0,1'],
            'id_unit'           => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
        ]); // <-- Tanda penutup ini sebelumnya hilang yang menyebabkan error syntax!

        // 2. Cari data model berdasarkan ID
        $risk = SmapMonitoring::findOrFail($id);

        // 3. Konversi nilai status dari string '0'/'1' menjadi integer (0 atau 1)
        $validated['status'] = (int) $validated['status'];

        // 4. 🔥 VALIDASI BACK-END: Hitung otomatis ID Level berdasarkan Skor Inherent demi keamanan data master
        $getBackendRiskLevelId = function($score) {
            $val = (int) $score;
            if ($val >= 1 && $val <= 5) return 1;          // Low
            if ($val >= 6 && $val <= 11) return 2;         // Low to Moderate
            if ($val >= 12 && $val <= 15) return 3;        // Moderate
            if ($val >= 16 && $val <= 19) return 4;        // Moderate to High
            if ($val >= 20 && $val <= 25) return 5;        // High
            return 1;
        };

        $validated['id_level'] = $getBackendRiskLevelId($validated['inherent']);
        $validated['id_level_target'] = $getBackendRiskLevelId($validated['inherent_target']);

        // 5. Update data ke database secara aman
        $risk->update($validated);

        // 6. Kembalikan ke halaman index dengan notifikasi sukses
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
        $risk = SmapMonitoring::with([
            'unitKerja',
            'kategoriRisiko',
            'levelRisiko',
            'levelTarget',
            'detailPeriode.period'
        ])->findOrFail($id);

        $periods = Period::orderBy('year', 'desc')->orderBy('quarter', 'asc')->get();

        $historyData = [];
        if ($risk->detailPeriode) {
            foreach ($risk->detailPeriode as $history) {
                $qKey = $history->quarter;

                if (str_contains($qKey, 'Q')) {
                    $qKey = str_replace('Q', 'TW', $qKey);
                } elseif (is_numeric($qKey)) {
                    $qKey = 'TW' . $qKey;
                }

                $historyData[$history->year][$qKey] = [
                    'value' => (int) $history->value,
                ];
            }
        }

        return view('smap.show', compact('risk', 'periods', 'historyData'));
    }

    public function storeMonitoring(Request $request, $id_smap)
    {
        $parentRisk = SmapMonitoring::findOrFail($id_smap);

        // 1. VALIDASI REQUEST FORM (Hapus validasi rentang kalkulasi level dinamis)
        $request->validate([
            'quarter'                 => 'required|in:TW1,TW2,TW3,TW4',
            'year'                    => 'required|numeric|min:2020|max:2099',
            'value'                   => 'required|numeric|min:1|max:25',
            'status_monitoring'       => 'required|in:0,1',
            'status_penanganan'       => 'required|in:belum,proses,selesai',
        ]);

        // 2. MAPPING PENAMAAN PERIODE KUARTAL
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

        // 3. CEK PROTEKSI DUPLIKASI INPUT DATA PERIODE
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

        // Mengunci Inherent dan Target mutlak ke data awal master risiko
        $scoreInherent   = (int)$parentRisk->inherent;
        $scoreTarget     = (int)$parentRisk->inherent_target;
        $idLevelTarget   = $parentRisk->id_level_target;

        $scoreCurrent    = (int)$request->value;

        // Helper untuk menentukan ID Level Current berjalan berdasarkan skor inputan
        $determineLevelId = function($score) {
            if ($score >= 1 && $score <= 5)   return 1;
            if ($score >= 6 && $score <= 11)  return 2;
            if ($score >= 12 && $score <= 15) return 3;
            if ($score >= 16 && $score <= 19) return 4;
            if ($score >= 20 && $score <= 25) return 5;
            return 1;
        };

        $levelInherentId = $determineLevelId($scoreInherent);
        $levelCurrentId  = $determineLevelId($scoreCurrent);

        // Klasifikasi Kelompok Level untuk Matriks Efektivitas
        $isLevelAman      = in_array($levelCurrentId, [1, 2]);
        $isLevelBelumAman = in_array($levelCurrentId, [3, 4, 5]);

        $efektivitasFinal = '';

        if ($scoreCurrent === $scoreInherent && $isLevelAman) {
            $efektivitasFinal = 'Pencatatan';
        } elseif ($scoreCurrent < $scoreInherent && $isLevelAman) {
            $efektivitasFinal = 'Effective';
        } elseif ($scoreCurrent < $scoreInherent && $isLevelBelumAman && $levelCurrentId < $levelInherentId) {
            $efektivitasFinal = 'Mostly Effective';
        } elseif ($scoreCurrent < $scoreInherent && $isLevelBelumAman && $levelCurrentId === $levelInherentId) {
            $efektivitasFinal = 'Partially Effective';
        } elseif ($scoreCurrent === $scoreInherent && $isLevelBelumAman) {
            $efektivitasFinal = 'In-Effective';
        } else {
            $efektivitasFinal = 'Unmeasurable';
        }

        $calculatedTrend = 'Stabil';
        if ($scoreCurrent > $scoreInherent) {
            $calculatedTrend = 'Naik';
        } elseif ($scoreCurrent < $scoreInherent) {
            $calculatedTrend = 'Turun';
        }

        // 4. SIMPAN DATA LENGKAP KE DATABASE
        \App\Models\SmapMonitoringPeriod::create([
            'id_smap'           => $parentRisk->id_smap,
            'quarter'           => $request->quarter,
            'year'              => $request->year,
            'id_level'          => $levelCurrentId,
            'id_level_target'   => $idLevelTarget,
            'value'             => $scoreCurrent,
            'inherent'          => $scoreInherent,
            'inherent_target'   => $scoreTarget,
            'trend'             => $calculatedTrend,
            'status_penanganan' => $request->status_penanganan,
            'efektif_risiko'    => $efektivitasFinal,
        ]);

        $parentRisk->update([
            'status' => $request->status_monitoring
        ]);

        return redirect()->back()
            ->with('success', "Berhasil merekam perkembangan & target risiko untuk periode {$periodName}!");
    }

    /**
     * Memperbarui data log monitoring kuartal tertentu (Inline Edit)
     */
    public function updateMonitoring(Request $request, string $id_detail): RedirectResponse
    {
        // 1. Validasi input (Kuartal dan Tahun sekarang masuk ke dalam validasi wajib)
        $validated = $request->validate([
            'quarter'           => ['required', 'in:TW1,TW2,TW3,TW4'],
            'year'              => ['required', 'numeric', 'min:2020', 'max:2099'],
            'value'             => ['required', 'integer', 'between:1,25'],
            'status_penanganan' => ['required', 'string', 'in:belum,proses,selesai'],
            'status'            => ['required', 'in:0,1'],
        ]);

        // 2. Cari data detail periode berdasarkan ID detail
        $history = SmapMonitoringPeriod::findOrFail($id_detail);

        // 3. MAPPING & MANAJEMEN UPDATE PERIODE (KUARTAL & TAHUN)
        $quarterMapping = [
            'TW1' => ['numeric' => '1', 'text' => 'TW1'],
            'TW2' => ['numeric' => '2', 'text' => 'TW2'],
            'TW3' => ['numeric' => '3', 'text' => 'TW3'],
            'TW4' => ['numeric' => '4', 'text' => 'TW4'],
        ];

        $selectedQuarter = $quarterMapping[$validated['quarter']]['numeric'];
        $quarterText     = $quarterMapping[$validated['quarter']]['text'];
        $periodName      = $quarterText . ' ' . $validated['year'];

        // Cari atau buat periode baru jika kombinasi TW + Tahun tersebut belum ada di database
        $period = Period::firstOrCreate(
            ['period_name' => $periodName],
            [
                'year'    => $validated['year'],
                'quarter' => $selectedQuarter,
            ]
        );

        // 4. Ambil data master risiko induk (SmapMonitoring)
        $riskMaster = SmapMonitoring::query()->where('id_smap', $history->id_smap ?? null)->first();
        $inherentScore = $riskMaster ? (int)$riskMaster->inherent : 0;

        // 5. Hitung otomatis ID Level berdasarkan Score Current baru
        $getBackendRiskLevelId = function($score) {
            $val = (int) $score;
            if ($val >= 1 && $val <= 5) return 1;          // Low
            if ($val >= 6 && $val <= 11) return 2;         // Low to Moderate
            if ($val >= 12 && $val <= 15) return 3;        // Moderate
            if ($val >= 16 && $val <= 19) return 4;        // Moderate to High
            if ($val >= 20 && $val <= 25) return 5;        // High
            return 1;
        };

        // 6. Hitung otomatis trend perubahan dari skor inherent awal
        $currentScore = (int) $validated['value'];
        if ($currentScore > $inherentScore) {
            $calculatedTrend = 'Naik';
        } elseif ($currentScore < $inherentScore) {
            $calculatedTrend = 'Turun';
        } else {
            $calculatedTrend = 'Stabil';
        }

        // 7. Simpan data perkembangan ke tabel detail (smap_monitoring_periods)
        $history->quarter           = $validated['quarter'];
        $history->year              = $validated['year'];    
        $history->value             = $currentScore;
        $history->status_penanganan = $validated['status_penanganan'];
        $history->id_level          = $getBackendRiskLevelId($currentScore);
        $history->trend             = $calculatedTrend;

        // Hitung ulang matriks efektivitas mitigasi secara dinamis
        $levelCurrentId  = $getBackendRiskLevelId($currentScore);
        $levelInherentId = $getBackendRiskLevelId($inherentScore);
        $isLevelAman      = in_array($levelCurrentId, [1, 2]);
        $isLevelBelumAman = in_array($levelCurrentId, [3, 4, 5]);
        $efektivitasFinal = 'Unmeasurable';

        if ($currentScore === $inherentScore && $isLevelAman) {
            $efektivitasFinal = 'Pencatatan';
        } elseif ($currentScore < $inherentScore && $isLevelAman) {
            $efektivitasFinal = 'Effective';
        } elseif ($currentScore < $inherentScore && $isLevelBelumAman && $levelCurrentId < $levelInherentId) {
            $efektivitasFinal = 'Mostly Effective';
        } elseif ($currentScore < $inherentScore && $isLevelBelumAman && $levelCurrentId === $levelInherentId) {
            $efektivitasFinal = 'Partially Effective';
        } elseif ($currentScore === $inherentScore && $isLevelBelumAman) {
            $efektivitasFinal = 'In-Effective';
        }

        $history->efektif_risiko = $efektivitasFinal;
        $history->save();

        // 8. Update status ke master risiko induk (SmapMonitoring)
        if ($riskMaster) {
            $riskMaster->status = (int) $validated['status'];
            $riskMaster->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Riwayat perkembangan kuartal, periode waktu, dan status risiko berhasil diperbarui.');
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
