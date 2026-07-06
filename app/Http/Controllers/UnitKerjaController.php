<?php

namespace App\Http\Controllers;

use App\Models\TopUnitKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitKerjaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $unitKerja = TopUnitKerja::query()
            ->withCount('risiko')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('nama_unit', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_unit')
            ->paginate(15)
            ->withQueryString();

        return view('unit-kerja.index', compact('unitKerja', 'search'));
    }

    public function create(): View
    {
        return view('unit-kerja.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255', 'unique:top_unit_kerja,nama_unit'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        TopUnitKerja::query()->create([
            'nama_unit' => trim($validated['nama_unit']),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function edit(TopUnitKerja $unitKerja): View
    {
        return view('unit-kerja.edit', compact('unitKerja'));
    }

    public function update(Request $request, TopUnitKerja $unitKerja): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('top_unit_kerja', 'nama_unit')->ignore($unitKerja->id_unit, 'id_unit'),
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $unitKerja->update([
            'nama_unit' => trim($validated['nama_unit']),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(TopUnitKerja $unitKerja): RedirectResponse
    {
        $hasRelatedRisiko = $unitKerja->risiko()->exists();

        if ($hasRelatedRisiko) {
            return redirect()
                ->route('unit-kerja.index')
                ->with('error', 'Unit kerja tidak bisa dihapus karena masih terhubung dengan data risiko.');
        }

        TopUnitKerja::query()
            ->where('id_unit', $unitKerja->id_unit)
            ->delete();

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil dihapus.');
    }
}
