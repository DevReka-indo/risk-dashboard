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
            $selectedPeriode = $request->query('periode', 'all');
            $selectedYear    = (int) $request->query('tahun', date('Y'));

            $dashboardData = $this->dashboardService->getDashboardData($selectedPeriode, $selectedYear);

            return view('departemen.index', array_merge([
                'tab' => $tab,
                'selectedPeriode' => $selectedPeriode,
                'selectedYear' => $selectedYear
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

        // 1. Cek Duplikasi
        if ($this->riskRepo->checkPeriodExists($id, $reqQuarter, $reqYear)) {
            return redirect()->back()->with('error', "Periode {$reqQuarter} Tahun {$reqYear} sudah terdaftar.");
        }

        // 2. Validasi Wajib Isi Berurutan & Tentukan Triwulan Sebelumnya
        $quarterOrder = ['TW1' => 1, 'TW2' => 2, 'TW3' => 3, 'TW4' => 4];
        $currentQNum = $quarterOrder[$reqQuarter];

        $prevQ = null;
        $prevYear = $reqYear;

        if ($currentQNum > 1) {
            $prevQ = array_search($currentQNum - 1, $quarterOrder);
            // Cek apakah riwayat berurutan di tahun yang sama sudah diisi
            if (!$this->riskRepo->getPeriodData($id, $prevQ, $reqYear)) {
                return redirect()->back()->with('error', "Gagal! Isi riwayat $prevQ Tahun $reqYear terlebih dahulu.");
            }
        } else {
            // Jika Triwulan saat ini adalah TW1, maka Triwulan sebelumnya adalah TW4 di tahun sebelumnya
            $prevQ = 'TW4';
            $prevYear = $reqYear - 1;
        }

        // 3. Kalkulasi Logika Bisnis (Nilai Inheren Dinamis)
        $risk = $this->riskRepo->findById($id);

        // Ambil data triwulan sebelumnya melalui Repository
        $prevPeriod = $this->riskRepo->getPeriodData($id, $prevQ, $prevYear);

        // Jika ada data historis, ambil value-nya. Jika tidak ada, ambil default dari master risk.
        $calculatedInherent = $prevPeriod ? $prevPeriod->value : $risk->inherent;

        $levelRecord = $this->riskRepo->findLevelByIdOrName($request->calculated_level);
        $idLevelTerbaru = $levelRecord ? $levelRecord->id_level : $risk->id_level;

        // Hitung Efektivitas Risiko menggunakan Inheren yang baru dikalkulasi
        $efektifRisiko = $this->dashboardService->hitungEfektivitasRisiko(
            (int) $request->value, (int) $calculatedInherent, (int) $idLevelTerbaru, (int) $risk->id_level
        );

        // 4. Simpan Data ke Pivot
        $this->riskRepo->attachPeriod($id, $idLevelTerbaru, [
            'quarter'         => $reqQuarter,
            'year'            => $reqYear,
            'value'           => $request->value,
            'inherent'        => $calculatedInherent, // <-- Inheren kini diset otomatis oleh sistem
            'trend'           => $request->calculated_trend,
            'target_value'    => $risk->target_value,
            'target_id_level' => $risk->target_id_level,
            'progres_belum'   => $request->progres_belum ?? 0,
            'progres_proses'  => $request->progres_proses ?? 0,
            'progres_sudah'   => $request->progres_sudah ?? 0,
            'efektif_risiko'  => $efektifRisiko,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 5. Update Data Terbaru ke Tabel Utama
        $this->riskRepo->update($id, [
            'id_level'       => $idLevelTerbaru,
            'value'          => $request->value,
            'trend'          => $request->calculated_trend,
            'progres_belum'  => $request->progres_belum ?? 0,
            'progres_proses' => $request->progres_proses ?? 0,
            'progres_sudah'  => $request->progres_sudah ?? 0,
            'status'         => $request->has('status_monitoring') ? $request->status_monitoring : $risk->status,
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

        // Update Data Terdaftar di Pivot
        $this->riskRepo->updatePivotPeriod($pivotId, [
            'id_level'       => $idLevelTerbaru,
            'value'          => $request->value,
            'trend'          => $request->calculated_trend,
            'progres_belum'  => $request->progres_belum ?? 0,
            'progres_proses' => $request->progres_proses ?? 0,
            'progres_sudah'  => $request->progres_sudah ?? 0,
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
