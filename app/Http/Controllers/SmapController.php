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
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $unitId = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId = $request->string('level_id')->toString();
        $trend = $request->string('trend')->toString();
        $status = $request->string('status')->toString();

        $smapRisks = SmapMonitoring::query()
            ->whereNull('parent_id') // KUNCI: Hanya tampilkan risiko utama/induk di halaman depan
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'latestPeriode.period']) // Eager loading periode terbaru
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
        $validated['id_period']  = null; // atau isi dengan ID periode aktif saat ini
        $validated['id_level']   = 1;    // nilai default sementara (sesuaikan dengan ID di tabel level_risiko)
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

        $levels = LevelRisiko::all();

        return view('smap.edit', compact('risk', 'units', 'categories', 'levels'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit' => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori' => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'id_level' => ['required', 'integer', 'exists:level_risiko,id_level'],
            'risk_event_deta' => ['required', 'string'],
            'value' => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'trend' => ['required', 'in:Naik,Turun,Stabil'],
            'status' => ['required', 'boolean'],
        ]);

        $risk = SmapMonitoring::findOrFail($id);
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
        // Muat detail beserta semua riwayat perkembangan periodenya
        $risk = SmapMonitoring::with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'detailPeriode.period'])->findOrFail($id);

        // Ambil semua master periode untuk pilihan dropdown select di form detail
        $periods = Period::orderBy('year', 'desc')->orderBy('quarter', 'asc')->get();

        return view('smap.show', compact('risk', 'periods'));
    }

public function storeMonitoring(Request $request, $id)
{
    // 1. Validasi input dari form dengan penambahan validasi untuk value dan inherent
    $request->validate([
        'quarter'           => 'required|in:Q1,Q2,Q3,Q4',
        'year'              => 'required|numeric|min:2020|max:2099',
        'value'             => 'required|numeric|min:1|max:25',
        'inherent'          => 'required|numeric',
        'status_monitoring' => 'required|in:0,1',
    ]);

    // 2. Mapping format "Q1" dari form menjadi Angka "1" dan teks "TW1"
    $quarterMapping = [
        'Q1' => ['numeric' => '1', 'text' => 'TW1'],
        'Q2' => ['numeric' => '2', 'text' => 'TW2'],
        'Q3' => ['numeric' => '3', 'text' => 'TW3'],
        'Q4' => ['numeric' => '4', 'text' => 'TW4'],
    ];

    $selectedQuarter = $quarterMapping[$request->quarter]['numeric'];
    $quarterText     = $quarterMapping[$request->quarter]['text'];

    // 3. Format nama periode sesuai database (Contoh: "TW1 2026")
    $periodName = $quarterText . ' ' . $request->year;

    // 4. Cari periode, jika tidak ada, buat baru
    $period = Period::firstOrCreate(
        ['period_name' => $periodName],
        [
            'year'    => $request->year,
            'quarter' => $selectedQuarter,
        ]
    );

    // 5. Dapatkan data risiko induk utama (Parent)
    $parentRisk = SmapMonitoring::findOrFail($id);

    // 6. Buat data log perkembangan baru (Child)
    // PERBAIKAN: Mengambil data dari $request (input user) bukan dari $parentRisk
    SmapMonitoring::create([
        'parent_id'       => $parentRisk->id_smap,
        'id_period'       => $period->id_period,
        'id_unit'         => $parentRisk->id_unit,
        'id_kategori'     => $parentRisk->id_kategori,
        'id_level'        => $parentRisk->id_level,
        'risk_event_deta' => $parentRisk->risk_event_deta,

        // Data diambil dari input form agar tidak 0
        'inherent'        => $request->inherent,
        'trend'           => $request->calculated_trend,
        'value'           => $request->value,

        'status'          => $request->status_monitoring,
    ]);

    return redirect()->back()->with('success', "Berhasil merekam perkembangan risiko untuk periode {$periodName}!");
}

public function destroyMonitoring($id)
{
    $monitoring = SmapMonitoring::findOrFail($id);

    $idSmap = $monitoring->parent_id;

    $monitoring->delete();

    if (!$idSmap) {
        return redirect()->route('smap-risk.index')
                         ->with('success', 'Riwayat berhasil dihapus.');
    }

    return redirect()->route('smap-risk.show', ['id' => $idSmap])
                     ->with('success', 'Riwayat monitoring berhasil dihapus.');
}
}
