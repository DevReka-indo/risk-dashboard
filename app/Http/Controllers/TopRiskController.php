<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopRiskRequest;
use App\Http\Requests\StoreTopMonitoringRequest;
use App\Http\Requests\UpdateTopMonitoringRequest;
use App\Models\TopMonitoringBulanan;
use App\Models\TopRisiko;
use App\Repositories\TopRiskRepository;
use App\Services\TopRiskDashboardService;
use App\Services\TopRiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopRiskController extends Controller
{
    public function __construct(
        private readonly TopRiskRepository $topRiskRepository,
        private readonly TopRiskService $topRiskService,
        private readonly TopRiskDashboardService $dashboardService
    ) {}

    public function index(Request $request): View
    {
        //[cite: 1] Penarikan parameter pencarian dan filter dari request.
        $search = $request->string('search')->toString();
        $kategoriId = $request->integer('id_kategori');
        $unitId = $request->integer('id_unit');
        $statusAktif = $request->string('status')->toString();
        $selectedMonth = (int) $request->integer('bulan', now()->month);
        $selectedYear = (int) $request->integer('tahun', now()->year);

        //[cite: 1] Memanggil layanan dan repositori untuk mendapatkan data.
        $topRisks = $this->topRiskRepository->getPaginatedRisks($search, $kategoriId, $unitId, $statusAktif);
        $kategoriRisiko = $this->topRiskRepository->getAllKategoriRisiko();
        $unitKerja = $this->topRiskRepository->getAllUnitKerja();

        $nilaiTopRisk = $this->topRiskRepository->getActiveTopRiskValues()->map(function (TopRisiko $topRisk): ?array {
            $monitoringTerbaru = $topRisk->monitoringBulanan->first();
            if ($monitoringTerbaru === null) {
                return null;
            }
            return [
                'nama_peristiwa_risiko' => $topRisk->nama_peristiwa_risiko,
                'nilai' => $monitoringTerbaru->nilai,
                'level' => $monitoringTerbaru->level?->nama_level,
                'kode_warna' => $monitoringTerbaru->level?->kode_warna,
                'bulan' => $monitoringTerbaru->bulan,
                'tahun' => $monitoringTerbaru->tahun,
            ];
        })->filter()->values();

        $dashboardData = $this->dashboardService->buildTopRiskDashboardData($selectedMonth, $selectedYear);

        return view('top-risk.index', compact(
            'topRisks', 'kategoriRisiko', 'unitKerja', 'nilaiTopRisk',
            'search', 'kategoriId', 'unitId', 'statusAktif',
            'selectedMonth', 'selectedYear', 'dashboardData'
        ));
    }

    public function create(): View
    {
        $kategoriRisiko = $this->topRiskRepository->getAllKategoriRisiko();
        $unitKerja = $this->topRiskRepository->getAllUnitKerja();

        return view('top-risk.create', compact('kategoriRisiko', 'unitKerja'));
    }

    public function store(TopRiskRequest $request): RedirectResponse
    {
        //[cite: 1] Pendelegasian logic store ke service.
        $this->topRiskService->createTopRisk($request->validated());

        return redirect()->route('top-risk.index')->with('success', 'Data Top Risk berhasil ditambahkan.');
    }

    public function show(TopRisiko $topRisk): View
    {
        $topRisk->load([
            'kategori',
            'unitKerja',
            'monitoringBulanan' => function ($query): void {
                $query->with(['level', 'aturanEfektivitas'])
                      ->orderByDesc('tahun')
                      ->orderByDesc('bulan');
            },
        ]);

        $levelRisiko = $this->topRiskRepository->getAllLevelRisiko();

        return view('top-risk.show', compact('topRisk', 'levelRisiko'));
    }

    public function edit(TopRisiko $topRisk): View
    {
        $topRisk->load('unitKerja');
        $kategoriRisiko = $this->topRiskRepository->getAllKategoriRisiko();
        $unitKerja = $this->topRiskRepository->getAllUnitKerja();

        return view('top-risk.edit', compact('topRisk', 'kategoriRisiko', 'unitKerja'));
    }

    public function update(TopRiskRequest $request, TopRisiko $topRisk): RedirectResponse
    {
        $this->topRiskService->updateTopRisk($topRisk, $request->validated());

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data Top Risk berhasil diperbarui.');
    }

    public function destroy(TopRisiko $topRisk): RedirectResponse
    {
        //[cite: 1] Logic destroy bawaan dari resource.
        TopRisiko::query()->where('id_risiko', $topRisk->id_risiko)->delete();

        return redirect()->route('top-risk.index')->with('success', 'Data Top Risk berhasil dihapus.');
    }

    public function storeMonitoring(StoreTopMonitoringRequest $request, TopRisiko $topRisk): RedirectResponse
    {
        $this->topRiskService->storeMonitoring($topRisk, $request->validated());

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil ditambahkan.');
    }

    public function updateMonitoring(UpdateTopMonitoringRequest $request, TopRisiko $topRisk, TopMonitoringBulanan $monitoring): RedirectResponse
    {
        abort_if($monitoring->id_risiko !== $topRisk->id_risiko, 404);

        $this->topRiskService->updateMonitoring($monitoring, $request->validated());

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil diperbarui.');
    }

    public function destroyMonitoring(TopRisiko $topRisk, TopMonitoringBulanan $monitoring): RedirectResponse
    {
        abort_if($monitoring->id_risiko !== $topRisk->id_risiko, 404);

        TopMonitoringBulanan::query()->where('id_monitoring', $monitoring->id_monitoring)->delete();

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil dihapus.');
    }
}
