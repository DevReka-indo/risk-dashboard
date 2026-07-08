<?php

namespace App\Http\Controllers;

use App\Models\TopUnitKerja;
use App\Models\DepMonitoring;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartemenController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $unitId = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId = $request->string('level_id')->toString();
        $trend = $request->string('trend')->toString();
        $type = $request->string('type')->toString();
        $status = $request->string('status')->toString();

        $risks = DepMonitoring::query()
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
            ->when($type, function ($query) use ($type): void {
                $query->where('type', $type);
            })
            ->when($status !== '', function ($query) use ($status): void {
                $query->where('status', (bool) $status);
            })
            ->oldest('id_monitoring')
            ->paginate(10)
            ->withQueryString();

        $units = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels = LevelRisiko::all();

        return view('departemen.index', compact(
            'risks',
            'search',
            'unitId',
            'categoryId',
            'levelId',
            'trend',
            'type',
            'status',
            'units',
            'categories',
            'levels',
        ));
    }

    public function create(): View
    {
        $units = TopUnitKerja::all();
        $categories = KategoriRisiko::whereType('departement')->get();
        $levels = LevelRisiko::all();

        return view('departemen.create', compact('units', 'categories', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'id_level'        => ['required', 'integer', 'exists:level_risiko,id_level'], // Otomatis dari JS
            'risk_event_deta' => ['required', 'string'],
            'value'           => ['required', 'integer', 'min:1', 'max:25'],              // Input Manual
            'inherent'        => ['required', 'integer', 'min:1'],                        // Input Manual
            'trend'           => ['required', 'in:Naik,Turun,Stabil'],                    // Otomatis dari JS
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
        ]);

        // Langsung insert array hasil validasi ke database
        DepMonitoring::create($validated);

        return redirect()
            ->route('department-risk.index')
            ->with('success', 'Risk Department berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail data risiko departemen (Show).
     */
    public function show(int $id): View
    {
        $risk = DepMonitoring::with(['unitKerja', 'kategoriRisiko', 'levelRisiko'])->findOrFail($id);

        return view('departemen.show', compact('risk'));
    }

    public function edit(string $id): View
    {
        $risk = DepMonitoring::findOrFail($id);
        $units = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::query()
            ->where('type', 'departement')
            ->orderBy('nama_kategori', 'asc')
            ->get();
        $levels = LevelRisiko::all();

        return view('departemen.edit', compact('risk', 'units', 'categories', 'levels'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'id_level'        => ['required', 'integer', 'exists:level_risiko,id_level'], // Otomatis dari JS
            'risk_event_deta' => ['required', 'string'],
            'value'           => ['required', 'integer', 'min:1', 'max:25'],              // Input Manual
            'inherent'        => ['required', 'integer', 'min:1'],                        // Input Manual
            'trend'           => ['required', 'in:Naik,Turun,Stabil'],                    // Otomatis dari JS
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
        ]);

        $risk = DepMonitoring::findOrFail($id);

        // Langsung update array hasil validasi ke database
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
            ->route('department-risk.index', ['tab' => 'department'])
            ->with('success', 'Risk Department berhasil dihapus.');
    }
}
