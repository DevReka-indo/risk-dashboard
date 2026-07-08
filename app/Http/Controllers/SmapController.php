<?php

namespace App\Http\Controllers;

use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\SmapMonitoring;
use App\Models\TopUnitKerja;
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
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko'])
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
            'id_unit' => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori' => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'id_level' => ['required', 'integer', 'exists:level_risiko,id_level'],
            'risk_event_deta' => ['required', 'string'],
            'value' => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'trend' => ['required', 'in:Naik,Turun,Stabil'],
            'status' => ['required', 'boolean'],
        ]);

        SmapMonitoring::create($validated);

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $risk = SmapMonitoring::findOrFail($id);
        $units = TopUnitKerja::orderBy('nama_unit', 'asc')->get();

        // HANYA mengambil kategori bertipe smap dan diurutkan secara alfabetis
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
        // Mengambil data dengan eager loading agar semua nama relasi langsung terbaca di view detail
        $risk = SmapMonitoring::with(['unitKerja', 'kategoriRisiko', 'levelRisiko'])->findOrFail($id);

        return view('smap.show', compact('risk'));
    }
}
