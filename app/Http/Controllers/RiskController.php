<?php

namespace App\Http\Controllers;

use App\Models\TopUnitKerja;
use App\Models\VdptMonitoring;
use App\Models\VdsCategorie;
use App\Models\VdsLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RiskController extends Controller
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

        $risks = VdptMonitoring::query()
            ->with(['unit', 'category', 'level'])
            ->when($search, function ($query) use ($search): void {
                $query->where('risk_event_deta', 'like', "%{$search}%");
            })
            ->when($unitId, function ($query) use ($unitId): void {
                $query->where('id_unit', $unitId);
            })
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->where('id_category', $categoryId);
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
            ->paginate(15)
            ->withQueryString();

        $units = TopUnitKerja::orderBy('nama_unit')->get();
        $categories = VdsCategorie::orderBy('category_name')->get();
        $levels = VdsLevel::all();

        return view('risks.index', compact(
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
        $units = TopUnitKerja::orderBy('nama_unit')->get();
        $categories = VdsCategorie::orderBy('category_name')->get();
        $levels = VdsLevel::all();

        return view('risks.create', compact('units', 'categories', 'levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit' => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_category' => ['required', 'integer', 'exists:vds_categorie,id_category'],
            'id_level' => ['required', 'integer', 'exists:vds_level,id_level'],
            'risk_event_deta' => ['required', 'string'],
            'value' => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'trend' => ['required', 'in:Naik,Turun,Stabil'],
            'type' => ['required', 'in:Proyek,Non-Proyek'],
            'status' => ['required', 'boolean'],
        ]);

        VdptMonitoring::create($validated);

        return redirect()
            ->route('risks.index')
            ->with('success', 'Risk berhasil ditambahkan.');
    }

    public function edit(VdptMonitoring $risk): View
    {
        $units = TopUnitKerja::orderBy('nama_unit')->get();
        $categories = VdsCategorie::orderBy('category_name')->get();
        $levels = VdsLevel::all();

        return view('risks.edit', compact('risk', 'units', 'categories', 'levels'));
    }

    public function update(Request $request, VdptMonitoring $risk): RedirectResponse
    {
        $validated = $request->validate([
            'id_unit' => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_category' => ['required', 'integer', 'exists:vds_categorie,id_category'],
            'id_level' => ['required', 'integer', 'exists:vds_level,id_level'],
            'risk_event_deta' => ['required', 'string'],
            'value' => ['required', 'integer', 'min:1'],
            'inherent' => ['required', 'integer', 'min:1'],
            'trend' => ['required', 'in:Naik,Turun,Stabil'],
            'type' => ['required', 'in:Proyek,Non-Proyek'],
            'status' => ['required', 'boolean'],
        ]);

        $risk->update($validated);

        return redirect()
            ->route('risks.index')
            ->with('success', 'Risk berhasil diperbarui.');
    }

    public function destroy(VdptMonitoring $risk): RedirectResponse
    {
        $risk->delete();

        return redirect()
            ->route('risks.index')
            ->with('success', 'Risk berhasil dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $search = $request->string('search')->toString();
        $unitId = $request->string('unit_id')->toString();
        $categoryId = $request->string('category_id')->toString();
        $levelId = $request->string('level_id')->toString();
        $trend = $request->string('trend')->toString();
        $type = $request->string('type')->toString();
        $status = $request->string('status')->toString();

        $risks = VdptMonitoring::query()
            ->with(['unit', 'category', 'level'])
            ->when($search, function ($query) use ($search): void {
                $query->where('risk_event_deta', 'like', "%{$search}%");
            })
            ->when($unitId, function ($query) use ($unitId): void {
                $query->where('id_unit', $unitId);
            })
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->where('id_category', $categoryId);
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
            ->latest('id_monitoring')
            ->get();

        $filename = 'risk-department-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['No', 'Unit Kerja', 'Kategori', 'Risk Event', 'Value', 'Level', 'Inherent', 'Trend', 'Type', 'Status', 'Dibuat'];

        $rows = $risks->map(function (VdptMonitoring $risk, int $index) {
            return [
                $index + 1,
                $risk->unit?->nama_unit ?? '-',
                $risk->category?->category_name ?? '-',
                $risk->risk_event_deta,
                $risk->value,
                $risk->level?->level_name ?? '-',
                $risk->inherent,
                $risk->trend,
                $risk->type,
                $risk->status ? 'Aktif' : 'Non-Aktif',
                $risk->created_at?->format('d/m/Y'),
            ];
        });

        $callback = function () use ($columns, $rows): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
