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
        $tab        = $request->string('tab')->toString() ?: 'data';

        $risks = DepMonitoring::query()
            ->with([
                'unitKerja',
                'kategoriRisiko',
                'levelRisiko',
                'periods' => fn($query) => $query->orderBy('dep_monitoring_periods.year', 'desc')
                                                 ->orderBy('dep_monitoring_periods.quarter', 'desc')
            ])
            ->when($search, fn($q) => $q->where('risk_event_deta', 'like', "%{$search}%"))
            ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
            ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
            ->when($levelId, fn($q) => $q->where('id_level', $levelId))
            ->when($trend, fn($q) => $q->where('trend', $trend))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status !== '', fn($q) => $q->where('status', (bool) $status))
            ->when($periodId, fn($q) => $q->where('id_period', $periodId))
            ->oldest('id_monitoring')
            ->paginate(10)
            ->withQueryString();

        $units      = TopUnitKerja::orderBy('nama_unit', 'asc')->get();
        $categories = KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
        $levels     = LevelRisiko::all();
        $periods    = Period::orderBy('year', 'desc')->orderBy('quarter', 'desc')->get();

        // Default value untuk variabel Dashboard
        $chartLabels = [];
        $chartData   = [];
        $summary = [
            'total_risiko'    => 0,
            'risiko_aktif'    => 0,
            'rata_rata_nilai' => 0,
            'tren'            => 'Stagnan'
        ];
        $period = [
            'label'          => 'Saat Ini',
            'previous_label' => 'Bulan Sebelumnya'
        ];
        $heatmapData = [
            'risks' => [],
            'rows'  => [[], [], [], [], []]
        ];
        $levelDistribution    = collect([]);
        $categoryDistribution = collect([]);
        $statusDistribution   = collect([]);

        // Kalkulasi Chart hanya saat Tab Dashboard aktif
        if ($tab === 'dashboard') {
            $riskCounts = DepMonitoring::query()
                ->selectRaw('id_unit, count(*) as total', [])
                ->when($categoryId, fn($q) => $q->where('id_kategori', $categoryId))
                ->when($levelId, fn($q) => $q->where('id_level', $levelId))
                ->when($trend, fn($q) => $q->where('trend', $trend))
                ->when($type, fn($q) => $q->where('type', $type))
                ->when($status !== '', fn($q) => $q->where('status', (bool) $status))
                ->when($periodId, fn($q) => $q->where('id_period', $periodId))
                ->groupBy('id_unit')
                ->pluck('total', 'id_unit');

            foreach ($units as $unit) {
                if ($unitId && $unit->id_unit != $unitId) continue;

                $chartLabels[] = $unit->nama_unit;
                $chartData[]   = $riskCounts->get($unit->id_unit, 0);
            }
        }

        return view('departemen.index', compact(
            'risks', 'search', 'unitId', 'categoryId', 'levelId', 'trend',
            'type', 'status', 'periodId', 'units', 'categories', 'levels',
            'periods', 'tab', 'chartLabels', 'chartData', 'summary', 'period',
            'heatmapData', 'levelDistribution', 'categoryDistribution', 'statusDistribution'
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
