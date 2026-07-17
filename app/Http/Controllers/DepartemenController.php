<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Form Requests (Validasi)
use App\Http\Requests\DepMonitoringRequest;
use App\Http\Requests\StoreDepMonitoringPeriodRequest;
use App\Http\Requests\UpdateDepMonitoringPeriodRequest;

// Services & Repositories
use App\Services\DepartemenRiskDashboardService;
use App\Repositories\DepartemenRiskRepository;

class DepartemenController extends Controller
{
    protected $dashboardService;
    protected $riskRepo;

    public function __construct(DepartemenRiskDashboardService $dashboardService, DepartemenRiskRepository $riskRepo)
    {
        $this->dashboardService = $dashboardService;
        $this->riskRepo = $riskRepo;
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'data');

        // 1. Tab Dashboard
        if ($tab === 'dashboard') {
            // TANGKAP VARIABEL DARI URL
            $selectedPeriode = $request->query('periode', 'all');
            $selectedYear    = (int) $request->query('tahun', date('Y'));

            $dashboardData = $this->dashboardService->getDashboardData($selectedPeriode, $selectedYear);

            // KIRIM VARIABELNYA KE VIEW (Tambahkan di dalam array_merge)
            return view('departemen.index', array_merge([
                'tab' => $tab,
                'selectedPeriode' => $selectedPeriode, // <--- INI OBATNYA
                'selectedYear' => $selectedYear        // <--- INI OBATNYA
            ], $dashboardData));
        }

        // 2. Tab Data
        $risks = $this->riskRepo->getPaginatedRisks($request->all());
        $units = $this->riskRepo->getAllUnits();
        $categories = $this->riskRepo->getAllCategories();
        $levels = $this->riskRepo->getAllLevels();

        return view('departemen.index', array_merge(
            compact('tab', 'risks', 'units', 'categories', 'levels'),
            $request->only(['search', 'unit_id', 'category_id', 'level_id', 'type', 'status'])
        ));
    }

    public function create(): View
    {
        return view('departemen.create', [
            'units' => $this->riskRepo->getAllUnits(),
            'categories' => $this->riskRepo->getAllCategories(),
            'levels' => $this->riskRepo->getAllLevels()
        ]);
    }

    public function store(DepMonitoringRequest $request): RedirectResponse
    {
        $dataToSave = $request->validated();
        $dataToSave['value'] = 0;
        $dataToSave['trend'] = 'Stabil';

        $this->riskRepo->create($dataToSave);

        return redirect()->route('department-risk.index', ['tab' => 'data'])
                         ->with('success', 'Risk Department berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        return view('departemen.edit', [
            'risk' => $this->riskRepo->findById($id),
            'units' => $this->riskRepo->getAllUnits(),
            'categories' => $this->riskRepo->getAllCategories(),
            'levels' => $this->riskRepo->getAllLevels()
        ]);
    }

    public function update(DepMonitoringRequest $request, string $id): RedirectResponse
    {
        $this->riskRepo->update($id, $request->validated());

        return redirect()->route('department-risk.index', ['tab' => 'data'])
                         ->with('success', 'Risk Department berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->riskRepo->delete($id);
        return redirect()->route('department-risk.index', ['tab' => 'data'])
                         ->with('success', 'Risk Department berhasil dihapus.');
    }

    public function show(string $id): View
    {
        return view('departemen.show', [
            'risk' => $this->riskRepo->findById($id),
            'levels' => $this->riskRepo->getLevelsOrdered()
        ]);
    }

    public function updatePeriod(StoreDepMonitoringPeriodRequest $request, string $id): RedirectResponse
    {
        $reqYear = $request->year;
        $reqQuarter = $request->quarter;

        // Cek Duplikasi
        if ($this->riskRepo->checkPeriodExists($id, $reqQuarter, $reqYear)) {
            return redirect()->back()->with('error', "Periode {$reqQuarter} Tahun {$reqYear} sudah terdaftar.");
        }

        // Validasi Wajib Isi Berurutan
        $quarterOrder = ['TW1' => 1, 'TW2' => 2, 'TW3' => 3, 'TW4' => 4];
        $currentQNum = $quarterOrder[$reqQuarter];

        if ($currentQNum > 1) {
            $prevQ = array_search($currentQNum - 1, $quarterOrder);
            if (!$this->riskRepo->getPeriodData($id, $prevQ, $reqYear)) {
                return redirect()->back()->with('error', "Gagal! Isi riwayat $prevQ Tahun $reqYear terlebih dahulu.");
            }
        }

        // Kalkulasi Logika Bisnis
        $risk = $this->riskRepo->findById($id);
        $levelRecord = $this->riskRepo->findLevelByIdOrName($request->calculated_level);
        $idLevelTerbaru = $levelRecord ? $levelRecord->id_level : $risk->id_level;

        $efektifRisiko = $this->dashboardService->hitungEfektivitasRisiko(
            (int) $request->value, (int) $risk->inherent, (int) $idLevelTerbaru, (int) $risk->id_level
        );

        // Simpan Data
        $this->riskRepo->attachPeriod($id, $idLevelTerbaru, [
            'quarter'         => $reqQuarter,
            'year'            => $reqYear,
            'value'           => $request->value,
            'inherent'        => $risk->inherent,
            'trend'           => $request->calculated_trend,
            'target_value'    => $risk->target_value,
            'target_id_level' => $risk->target_id_level,
            'penanganan'      => $request->penanganan,
            'efektif_risiko'  => $efektifRisiko,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->riskRepo->update($id, [
            'id_level'   => $idLevelTerbaru,
            'value'      => $request->value,
            'trend'      => $request->calculated_trend,
            'penanganan' => $request->penanganan,
            'status'     => $request->has('status_monitoring') ? $request->status_monitoring : $risk->status,
        ]);

        return redirect()->route('department-risk.show', $id)
                         ->with('success', "Data parameter triwulan $reqQuarter berhasil ditambahkan.");
    }

    public function updateExistingPeriod(UpdateDepMonitoringPeriodRequest $request, string $id, string $pivotId): RedirectResponse
    {
        $risk = $this->riskRepo->findById($id);
        $pivot = $this->riskRepo->getPivotById($pivotId);

        if (!$pivot) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $levelRecord = $this->riskRepo->findLevelByIdOrName($request->calculated_level);
        $idLevelTerbaru = $levelRecord ? $levelRecord->id_level : $risk->id_level;

        $efektifRisiko = $this->dashboardService->hitungEfektivitasRisiko(
            (int) $request->value, (int) $pivot->inherent, (int) $idLevelTerbaru, (int) $risk->id_level
        );

        $this->riskRepo->updatePivotPeriod($pivotId, [
            'id_level'       => $idLevelTerbaru,
            'value'          => $request->value,
            'trend'          => $request->calculated_trend,
            'penanganan'     => $request->penanganan,
            'efektif_risiko' => $efektifRisiko,
            'updated_at'     => now(),
        ]);

        return redirect()->route('department-risk.show', $id)
                         ->with('success', "Data monitoring berhasil diperbarui.");
    }

    public function destroyPeriod(string $id, string $pivotId): RedirectResponse
    {
        $this->riskRepo->deletePivotPeriod($pivotId);
        return redirect()->route('department-risk.show', $id)->with('success', 'Data riwayat triwulan berhasil dihapus.');
    }
}
