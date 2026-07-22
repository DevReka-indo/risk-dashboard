<?php

namespace App\Http\Controllers;

use App\Models\SmapMonitoring;
use App\Models\SmapMonitoringPeriod;
use App\Models\Period;
use App\Http\Requests\SmapRiskRequest;
use App\Repositories\SmapRepository;
use App\Services\SmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class SmapController extends Controller
{
    protected SmapRepository $smapRepo;
    protected SmapService $smapService;

    /**
     * Inject Service dan Repository langsung di Constructor
     */
    public function __construct(SmapRepository $smapRepo, SmapService $smapService)
    {
        $this->smapRepo = $smapRepo;
        $this->smapService = $smapService;
    }

    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $tab = $request->query('tab', 'list');

        if ($tab === 'dashboard') {
            $defaultQuarter = ceil(date('n') / 3);
            $selectedPeriode = (int) $request->query('periode', $defaultQuarter);
            $yearParam = $request->query('tahun') ? (int) $request->query('tahun') : null;

            $data = $this->smapService->buildDashboardData($selectedPeriode, $yearParam);

            $data['dashboardData'] = [
                'summary'               => $data['summary'],
                'period'                => $data['periodText'],
                'level_distribution'    => $data['level_distribution'],
                'trend_risk'            => $data['trendData'],
                'category_distribution' => $data['catData'],
                'heatmap'               => [],
                'status_distribution'   => [],
            ];

            return view('smap.index', array_merge(
                ['tab' => $tab, 'selectedPeriode' => $selectedPeriode],
                $data
            ));
        }

        if ($request->has('reset')) {
            session()->forget('smap_risk_filter');
            return redirect()->route('smap-risk.index', ['tab' => 'list']); // Sesuaikan dengan nama route kamu
        }

        $filterParams = $request->except(['tab', 'page']);

        if (!empty(array_filter($filterParams))) {
            session(['smap_risk_filter' => $filterParams]);
        }

        elseif (session()->has('smap_risk_filter') && empty($filterParams) && !$request->has('page')) {
            return redirect()->route('smap-risk.index', array_merge(['tab' => 'list'], session('smap_risk_filter')));
        }

        $savedFilters = session('smap_risk_filter', []);

        $filters = [
            'search'      => $request->query('search', $savedFilters['search'] ?? ''),
            'unit_id'     => $request->query('unit_id', $savedFilters['unit_id'] ?? ''),
            'category_id' => $request->query('category_id', $savedFilters['category_id'] ?? ''),
            'level_id'    => $request->query('level_id', $savedFilters['level_id'] ?? ''),
            'trend'       => $request->query('trend', $savedFilters['trend'] ?? ''),
            'status'      => $request->query('status', $savedFilters['status'] ?? ''),
        ];

        $smapRisks = $this->smapRepo->getPaginatedRisks($filters);
        $units = $this->smapRepo->getAllUnits();
        $categories = $this->smapRepo->getAllCategories();
        $levels = $this->smapRepo->getAllLevels();

        return view('smap.index', array_merge(
            ['tab' => $tab, 'smapRisks' => $smapRisks],
            $filters,
            compact('units', 'categories', 'levels')
        ));
    }

    public function create(): View
    {
        $units = $this->smapRepo->getAllUnits();
        $categories = $this->smapRepo->getSmapCategories();
        $levels = $this->smapRepo->getAllLevels();

        return view('smap.create', compact('units', 'categories', 'levels'));
    }

    public function store(SmapRiskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['parent_id']  = null;
        $validated['id_period']  = null;
        $validated['value']      = 0;
        $validated['trend']      = 'Stabil';

        SmapMonitoring::create($validated);

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $risk = SmapMonitoring::findOrFail($id);
        $units = $this->smapRepo->getAllUnits();
        $categories = $this->smapRepo->getSmapCategories();

        return view('smap.edit', compact('risk', 'units', 'categories'));
    }

    public function update(SmapRiskRequest $request, string $id_smap): RedirectResponse
    {
        $validated = $request->validated();
        $risk = SmapMonitoring::query()->where('id_smap', $id_smap)->firstOrFail();

        $validated['status'] = (int) $validated['status'];
        $validated['id_level'] = $this->smapService->determineLevelId($validated['inherent']);
        $validated['id_level_target'] = $this->smapService->determineLevelId($validated['inherent_target']);

        $risk->created_at = Carbon::parse($validated['created_at']);
        $risk->update($validated);

        return redirect()
            ->route('smap-risk.index')
            ->with('success', 'Risk SMAP berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        SmapMonitoring::findOrFail($id)->delete();

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

        $periods = Period::orderBy('year', 'desc')
            ->orderBy('quarter', 'asc')
            ->get();

        $historyData = [];
        if ($risk->detailPeriode) {
            foreach ($risk->detailPeriode as $history) {
                if (str_contains($history->quarter, 'Q')) {
                    $qKey = str_replace('Q', 'TW', $history->quarter);
                } else {
                    $qKey = is_numeric($history->quarter) ? 'TW' . $history->quarter : $history->quarter;
                }

                $historyData[$history->year][$qKey] = ['value' => (int) $history->value];
            }
        }

        return view('smap.show', compact('risk', 'periods', 'historyData'));
    }

    public function storeMonitoring(SmapRiskRequest $request, $id_smap)
    {
        $parentRisk = SmapMonitoring::findOrFail($id_smap);
        $validated = $request->validated();

        $quarterMapping = [
            'TW1' => ['numeric' => '1', 'text' => 'TW1'],
            'TW2' => ['numeric' => '2', 'text' => 'TW2'],
            'TW3' => ['numeric' => '3', 'text' => 'TW3'],
            'TW4' => ['numeric' => '4', 'text' => 'TW4']
        ];

        $selectedQuarter = $quarterMapping[$validated['quarter']]['numeric'];
        $periodName = $quarterMapping[$validated['quarter']]['text'] . ' ' . $validated['year'];

        Period::firstOrCreate(
            ['period_name' => $periodName],
            ['year' => $validated['year'], 'quarter' => $selectedQuarter]
        );

        $exists = SmapMonitoringPeriod::query()
            ->where('id_smap', (int) $parentRisk->id_smap)
            ->where('quarter', $validated['quarter'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quarter' => "Monitoring untuk periode {$periodName} sudah pernah diinput."]);
        }

        // ⬇️ LOGIKA DYNAMIC CASCADING INHERENT ⬇️
        // Cari riwayat monitoring terakhir dari risiko ini
        $latestMonitoring = SmapMonitoringPeriod::query()
            ->where('id_smap', (int) $parentRisk->id_smap)
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->first();

        // Jika ada monitoring sebelumnya, pakai 'value' TW lalu sebagai 'inherent' TW ini.
        // Jika belum ada (input TW 1 pertama kali), pakai 'inherent' master.
        $scoreInherent = $latestMonitoring ? (int) $latestMonitoring->value : (int) $parentRisk->inherent;
        $scoreCurrent  = (int) $validated['value'];

        SmapMonitoringPeriod::create([
            'id_smap'         => $parentRisk->id_smap,
            'quarter'         => $validated['quarter'],
            'year'            => $validated['year'],
            'id_level'        => $this->smapService->determineLevelId($scoreCurrent),
            'id_level_target' => $parentRisk->id_level_target,
            'value'           => $scoreCurrent,
            'inherent'        => $scoreInherent, // Dynamic Inherent terpasang di sini
            'inherent_target' => (int) $parentRisk->inherent_target,
            'trend'           => $this->smapService->calculateTrend($scoreCurrent, $scoreInherent),
            'progress_belum'  => (int) $validated['progress_belum'],
            'progress_proses' => (int) $validated['progress_proses'],
            'progress_sudah'  => (int) $validated['progress_sudah'],
            'efektif_risiko'  => $this->smapService->calculateEfektivitas($scoreCurrent, $scoreInherent),
        ]);

        $parentRisk->update(['status' => $validated['status_monitoring']]);

        return redirect()
            ->back()
            ->with('success', "Berhasil merekam perkembangan & target risiko untuk periode {$periodName}!");
    }

    public function updateMonitoring(SmapRiskRequest $request, string $id_detail): RedirectResponse
    {
        $validated = $request->validated();
        $history = SmapMonitoringPeriod::findOrFail($id_detail);

        $quarterMapping = [
            'TW1' => ['numeric' => '1', 'text' => 'TW1'],
            'TW2' => ['numeric' => '2', 'text' => 'TW2'],
            'TW3' => ['numeric' => '3', 'text' => 'TW3'],
            'TW4' => ['numeric' => '4', 'text' => 'TW4']
        ];

        $selectedQuarter = $quarterMapping[$validated['quarter']]['numeric'];
        $periodName = $quarterMapping[$validated['quarter']]['text'] . ' ' . $validated['year'];

        Period::firstOrCreate(
            ['period_name' => $periodName],
            ['year' => $validated['year'], 'quarter' => $selectedQuarter]
        );

        $riskMaster = SmapMonitoring::query()->where('id_smap', $history->id_smap ?? null)->first();

        // Cari periode SEBELUM periode yang sedang di-update ini (exclude ID detail ini sendiri)
        $previousMonitoring = SmapMonitoringPeriod::query()
            ->where('id_smap', $history->id_smap)
            ->where('id_detail', '!=', $history->id_detail) // Sesuaikan nama Primary Key tabel history kamu (misal: 'id' / 'id_detail')
            ->where(function ($query) use ($validated) {
                $query->where('year', '<', $validated['year'])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('year', '=', $validated['year'])
                            ->where('quarter', '<', $validated['quarter']);
                    });
            })
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->first();

        // Jika ada periode sebelumnya, gunakan value-nya sebagai inherent. Jika tidak ada, panggil inherent Master.
        $inherentScore = $previousMonitoring
            ? (int) $previousMonitoring->value
            : ($riskMaster ? (int) $riskMaster->inherent : 0);

        $currentScore = (int) $validated['value'];

        $history->quarter         = $validated['quarter'];
        $history->year            = $validated['year'];
        $history->value           = $currentScore;
        $history->inherent        = $inherentScore; // Dynamic inherent ter-update
        $history->progress_belum  = (int) $validated['progress_belum'];
        $history->progress_proses = (int) $validated['progress_proses'];
        $history->progress_sudah  = (int) $validated['progress_sudah'];
        $history->id_level        = $this->smapService->determineLevelId($currentScore);
        $history->trend           = $this->smapService->calculateTrend($currentScore, $inherentScore);
        $history->efektif_risiko  = $this->smapService->calculateEfektivitas($currentScore, $inherentScore);
        $history->save();

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
        $monitoring = SmapMonitoringPeriod::findOrFail($id_period);
        $idSmapParent = $monitoring->id_smap;
        $monitoring->delete();

        return redirect()
            ->route('smap-risk.show', $idSmapParent)
            ->with('success', 'Riwayat monitoring berhasil dihapus.');
    }
}
