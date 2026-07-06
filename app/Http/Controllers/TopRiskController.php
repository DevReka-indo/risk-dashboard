<?php

namespace App\Http\Controllers;

use App\Models\TopAturanEfektivitas;
use App\Models\TopKategoriRisiko;
use App\Models\TopLevelRisiko;
use App\Models\TopMonitoringBulanan;
use App\Models\TopRisiko;
use App\Models\TopUnitKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TopRiskController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $kategoriId = $request->integer('id_kategori');
        $unitId = $request->integer('id_unit');
        $statusAktif = $request->string('status')->toString();

        $selectedMonth = (int) $request->integer('bulan', now()->month);
        $selectedYear = (int) $request->integer('tahun', now()->year);

        $topRisks = TopRisiko::query()
            ->with([
                'kategori',
                'unitKerja',
                'monitoringBulanan' => function ($query): void {
                    $query
                        ->with(['level', 'aturanEfektivitas'])
                        ->orderByDesc('tahun')
                        ->orderByDesc('bulan');
                },
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nama_peristiwa_risiko', 'like', "%{$search}%");
            })
            ->when($kategoriId > 0, function ($query) use ($kategoriId): void {
                $query->where('id_kategori', $kategoriId);
            })
            ->when($unitId > 0, function ($query) use ($unitId): void {
                $query->whereHas('unitKerja', function ($unitQuery) use ($unitId): void {
                    $unitQuery->where('top_unit_kerja.id_unit', $unitId);
                });
            })
            ->when($statusAktif !== '', function ($query) use ($statusAktif): void {
                $query->where('is_aktif', $statusAktif === 'aktif');
            })
            ->orderBy('nama_peristiwa_risiko', 'asc')
            ->paginate(10)
            ->withQueryString();

        $kategoriRisiko = TopKategoriRisiko::query()->orderBy('nama_kategori', 'asc')->get();

        $unitKerja = TopUnitKerja::query()->orderBy('nama_unit', 'asc')->get();

        $nilaiTopRisk = TopRisiko::query()
            ->with([
                'monitoringBulanan' => function ($query): void {
                    $query->with('level')->orderByDesc('tahun')->orderByDesc('bulan');
                },
            ])
            ->where('is_aktif', true)
            ->orderBy('nama_peristiwa_risiko', 'asc')
            ->get()
            ->map(function (TopRisiko $topRisk): ?array {
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
            })
            ->filter()
            ->values();

        $dashboardData = $this->buildTopRiskDashboardData(selectedMonth: $selectedMonth, selectedYear: $selectedYear);

        return view('top-risk.index', compact('topRisks', 'kategoriRisiko', 'unitKerja', 'nilaiTopRisk', 'search', 'kategoriId', 'unitId', 'statusAktif', 'selectedMonth', 'selectedYear', 'dashboardData'));
    }

    public function create(): View
    {
        $kategoriRisiko = TopKategoriRisiko::query()->orderBy('nama_kategori', 'asc')->get();

        $unitKerja = TopUnitKerja::query()->orderBy('nama_unit', 'asc')->get();

        return view('top-risk.create', compact('kategoriRisiko', 'unitKerja'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_peristiwa_risiko' => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', 'integer', 'exists:top_kategori_risiko,id_kategori'],
            'tanggal_dibuat' => ['required', 'date'],
            'is_aktif' => ['nullable', 'boolean'],
            'unit_kerja' => ['required', 'array', 'min:1'],
            'unit_kerja.*' => ['integer', 'exists:top_unit_kerja,id_unit'],
        ]);

        DB::transaction(function () use ($validated): void {
            $topRisk = TopRisiko::query()->create([
                'nama_peristiwa_risiko' => $validated['nama_peristiwa_risiko'],
                'id_kategori' => $validated['id_kategori'],
                'tanggal_dibuat' => $validated['tanggal_dibuat'],
                'is_aktif' => $validated['is_aktif'] ?? true,
            ]);

            $topRisk->unitKerja()->sync($validated['unit_kerja']);
        });

        return redirect()->route('top-risk.index')->with('success', 'Data Top Risk berhasil ditambahkan.');
    }

    public function show(TopRisiko $topRisk): View
    {
        $topRisk->load([
            'kategori',
            'unitKerja',
            'monitoringBulanan' => function ($query): void {
                $query
                    ->with(['level', 'aturanEfektivitas'])
                    ->orderByDesc('tahun')
                    ->orderByDesc('bulan');
            },
        ]);

        $levelRisiko = TopLevelRisiko::query()->orderBy('urutan', 'asc')->get();

        return view('top-risk.show', compact('topRisk', 'levelRisiko'));
    }

    public function edit(TopRisiko $topRisk): View
    {
        $topRisk->load('unitKerja');

        $kategoriRisiko = TopKategoriRisiko::query()->orderBy('nama_kategori', 'asc')->get();

        $unitKerja = TopUnitKerja::query()->orderBy('nama_unit', 'asc')->get();

        return view('top-risk.edit', compact('topRisk', 'kategoriRisiko', 'unitKerja'));
    }

    public function update(Request $request, TopRisiko $topRisk): RedirectResponse
    {
        $validated = $request->validate([
            'nama_peristiwa_risiko' => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', 'integer', 'exists:top_kategori_risiko,id_kategori'],
            'tanggal_dibuat' => ['required', 'date'],
            'is_aktif' => ['nullable', 'boolean'],
            'unit_kerja' => ['required', 'array', 'min:1'],
            'unit_kerja.*' => ['integer', 'exists:top_unit_kerja,id_unit'],
        ]);

        DB::transaction(function () use ($validated, $topRisk): void {
            TopRisiko::query()
                ->where('id_risiko', $topRisk->id_risiko)
                ->update([
                    'nama_peristiwa_risiko' => $validated['nama_peristiwa_risiko'],
                    'id_kategori' => $validated['id_kategori'],
                    'tanggal_dibuat' => $validated['tanggal_dibuat'],
                    'is_aktif' => $validated['is_aktif'] ?? false,
                ]);

            $topRisk->unitKerja()->sync($validated['unit_kerja']);
        });

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data Top Risk berhasil diperbarui.');
    }

    public function destroy(TopRisiko $topRisk): RedirectResponse
    {
        TopRisiko::query()->where('id_risiko', $topRisk->id_risiko)->delete();

        return redirect()->route('top-risk.index')->with('success', 'Data Top Risk berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | Top Risk Dashboard / Chart Data
    |--------------------------------------------------------------------------
    |
    | Sementara diletakkan di controller ini sampai dashboard utama selesai.
    | Data ini dipakai oleh tab Dashboard pada halaman index Top Risk.
    | Chart dibuat Tailwind-only dulu, tanpa tambahan JS library.
    |
    */

    private function buildTopRiskDashboardData(int $selectedMonth, int $selectedYear): array
    {
        $currentMonitoring = TopMonitoringBulanan::query()
            ->with(['risiko.kategori', 'risiko.unitKerja', 'level', 'aturanEfektivitas'])
            ->where('bulan', $selectedMonth)
            ->where('tahun', $selectedYear)
            ->get();

        [$previousMonth, $previousYear] = $this->resolvePreviousPeriod(selectedMonth: $selectedMonth, selectedYear: $selectedYear);

        $previousMonitoring = TopMonitoringBulanan::query()->where('bulan', $previousMonth)->where('tahun', $previousYear)->get();

        $averageCurrentValue = round((float) ($currentMonitoring->avg('nilai') ?? 0), 2);
        $averagePreviousValue = round((float) ($previousMonitoring->avg('nilai') ?? 0), 2);

        return [
            'period' => [
                'month' => $selectedMonth,
                'year' => $selectedYear,
                'label' => $this->monthName($selectedMonth) . ' ' . $selectedYear,
                'previous_label' => $this->monthName($previousMonth) . ' ' . $previousYear,
            ],
            'summary' => [
                'total_risiko' => TopRisiko::query()->count('*'),
                'risiko_aktif' => TopRisiko::query()->where('is_aktif', true)->count('*'),
                'rata_rata_nilai' => $averageCurrentValue,
                'tren' => $this->resolveTwoPeriodTrendLabel(currentValue: $averageCurrentValue, previousValue: $averagePreviousValue),
                'total_monitoring' => $currentMonitoring->count(),
            ],
            'heatmap' => $this->buildHeatmapData($currentMonitoring),
            'level_distribution' => $this->buildLevelDistribution($currentMonitoring),
            'category_distribution' => $this->buildCategoryDistribution($currentMonitoring),
            'status_distribution' => $this->buildStatusDistribution($currentMonitoring),
            'trend_risk' => $this->buildRiskTrendRows(currentMonitoring: $currentMonitoring, previousMonitoring: $previousMonitoring),
        ];
    }

    private function buildHeatmapData(Collection $currentMonitoring): array
    {
        $monitoringRows = $currentMonitoring
            ->sortByDesc('nilai')
            ->values()
            ->map(function (TopMonitoringBulanan $monitoring, int $index): array {
                return [
                    'code' => 'R' . ($index + 1),
                    'risk_name' => $monitoring->risiko?->nama_peristiwa_risiko ?? '-',
                    'value' => (int) $monitoring->nilai,
                    'level' => $monitoring->level?->nama_level ?? '-',
                ];
            });

        $cells = collect(range(25, 1))
            ->map(function (int $value) use ($monitoringRows): array {
                $risks = $monitoringRows->where('value', $value)->values();

                return [
                    'value' => $value,
                    'risks' => $risks,
                    'class' => $this->resolveHeatmapCellClass($value),
                ];
            })
            ->chunk(5)
            ->values()
            ->all();

        return [
            'rows' => $cells,
            'risks' => $monitoringRows,
        ];
    }

    private function resolveHeatmapCellClass(int $value): string
    {
        if ($value >= 21) {
            return 'bg-rose-100 text-rose-900 ring-rose-200';
        }

        if ($value >= 16) {
            return 'bg-orange-100 text-orange-900 ring-orange-200';
        }

        if ($value >= 11) {
            return 'bg-amber-100 text-amber-900 ring-amber-200';
        }

        if ($value >= 6) {
            return 'bg-lime-100 text-lime-900 ring-lime-200';
        }

        return 'bg-emerald-100 text-emerald-900 ring-emerald-200';
    }

    private function buildLevelDistribution(Collection $currentMonitoring): Collection
    {
        return TopLevelRisiko::query()
            ->orderBy('urutan', 'asc')
            ->get()
            ->map(function (TopLevelRisiko $levelRisiko) use ($currentMonitoring): array {
                $total = $currentMonitoring->where('id_level', $levelRisiko->id_level)->count();

                return [
                    'label' => $levelRisiko->nama_level,
                    'total' => $total,
                    'color' => $levelRisiko->kode_warna,
                ];
            });
    }

    private function buildCategoryDistribution(Collection $currentMonitoring): Collection
    {
        return TopKategoriRisiko::query()
            ->orderBy('nama_kategori', 'asc')
            ->get()
            ->map(function (TopKategoriRisiko $kategoriRisiko) use ($currentMonitoring): array {
                $total = $currentMonitoring
                    ->filter(function (TopMonitoringBulanan $monitoring) use ($kategoriRisiko): bool {
                        return (int) ($monitoring->risiko?->id_kategori ?? 0) === (int) $kategoriRisiko->id_kategori;
                    })
                    ->count();

                return [
                    'label' => $kategoriRisiko->nama_kategori,
                    'total' => $total,
                ];
            });
    }

    private function buildStatusDistribution(Collection $currentMonitoring): Collection
    {
        return collect([
            [
                'label' => 'Aktif',
                'total' => $currentMonitoring->where('status', 'Aktif')->count(),
            ],
            [
                'label' => 'Tidak Aktif',
                'total' => $currentMonitoring->where('status', 'Tidak Aktif')->count(),
            ],
        ]);
    }

    private function buildRiskTrendRows(Collection $currentMonitoring, Collection $previousMonitoring): Collection
    {
        return $currentMonitoring
            ->sortByDesc('nilai')
            ->values()
            ->map(function (TopMonitoringBulanan $monitoring, int $index): array {
                $trendAnalysis = $this->resolveRiskTrendAnalysis(idRisiko: (int) $monitoring->id_risiko, selectedMonth: (int) $monitoring->bulan, selectedYear: (int) $monitoring->tahun);

                return [
                    'number' => $index + 1,
                    'risk_name' => $monitoring->risiko?->nama_peristiwa_risiko ?? '-',
                    'current_value' => (int) $monitoring->nilai,
                    'trend' => $trendAnalysis['trend'],
                    'trend_description' => $trendAnalysis['description'],
                    'trend_values' => $trendAnalysis['values'],
                    'level' => $monitoring->level?->nama_level ?? '-',
                    'effectiveness' => $monitoring->aturanEfektivitas?->hasil ?? 'Belum ada pembanding',
                ];
            });
    }

    private function resolveRiskTrendAnalysis(int $idRisiko, int $selectedMonth, int $selectedYear): array
    {
        $selectedPeriod = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);

        $monitoringValues = TopMonitoringBulanan::query()
            ->where('id_risiko', $idRisiko)
            ->whereRaw("STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') <= ?", [$selectedPeriod])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get(['bulan', 'tahun', 'nilai'])
            ->map(function (TopMonitoringBulanan $monitoring): array {
                return [
                    'label' => $this->shortMonthName((int) $monitoring->bulan) . ' ' . $monitoring->tahun,
                    'value' => (int) $monitoring->nilai,
                ];
            })
            ->values();

        if ($monitoringValues->count() <= 1) {
            return [
                'trend' => 'Belum ada pembanding',
                'description' => 'Belum tersedia data bulan sebelumnya sebagai pembanding.',
                'values' => $monitoringValues,
            ];
        }

        $firstValue = (int) $monitoringValues->first()['value'];
        $lastValue = (int) $monitoringValues->last()['value'];

        $hasIncrease = false;
        $hasDecrease = false;

        for ($index = 1; $index < $monitoringValues->count(); $index++) {
            $previousValue = (int) $monitoringValues->get($index - 1)['value'];
            $currentValue = (int) $monitoringValues->get($index)['value'];

            if ($currentValue > $previousValue) {
                $hasIncrease = true;
            }

            if ($currentValue < $previousValue) {
                $hasDecrease = true;
            }
        }

        $allValuesAreSame = !$hasIncrease && !$hasDecrease;

        if ($allValuesAreSame) {
            return [
                'trend' => 'Stagnan',
                'description' => 'Semua nilai risiko sama pada seluruh periode monitoring.',
                'values' => $monitoringValues,
            ];
        }

        if (!$hasDecrease && $lastValue > $firstValue) {
            return [
                'trend' => 'Naik',
                'description' => 'Nilai risiko tidak pernah turun dan nilai akhir lebih besar dari nilai awal.',
                'values' => $monitoringValues,
            ];
        }

        if (!$hasIncrease && $lastValue < $firstValue) {
            return [
                'trend' => 'Turun',
                'description' => 'Nilai risiko tidak pernah naik dan nilai akhir lebih kecil dari nilai awal.',
                'values' => $monitoringValues,
            ];
        }

        return [
            'trend' => 'Fluktuatif',
            'description' => 'Pola nilai risiko campuran, terdapat perubahan naik dan turun antar bulan.',
            'values' => $monitoringValues,
        ];
    }

    private function resolvePreviousPeriod(int $selectedMonth, int $selectedYear): array
    {
        if ($selectedMonth === 1) {
            return [12, $selectedYear - 1];
        }

        return [$selectedMonth - 1, $selectedYear];
    }

private function resolveTwoPeriodTrendLabel(float $currentValue, float $previousValue): string
{
    if ($currentValue > $previousValue) {
        return 'Naik';
    }

    if ($currentValue < $previousValue) {
        return 'Turun';
    }

    return 'Stagnan';
}

    private function shortMonthName(int $month): string
    {
        return match ($month) {
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
            default => '-',
        };
    }

    private function monthName(int $month): string
    {
        return match ($month) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
            default => '-',
        };
    }

    public function storeMonitoring(Request $request, TopRisiko $topRisk): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12', Rule::unique('top_monitoring_bulanan', 'bulan')->where('tahun', $request->integer('tahun'))->where('id_risiko', $topRisk->id_risiko)],
            'tahun' => ['required', 'integer', 'digits:4', 'min:2000'],
            'nilai' => ['required', 'integer', 'min:0'],
            'id_level' => ['required', 'integer', 'exists:top_level_risiko,id_level'],
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'progres_belum' => ['nullable', 'integer', 'min:0'],
            'progres_proses' => ['nullable', 'integer', 'min:0'],
            'progres_sudah' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(idRisiko: $topRisk->id_risiko, bulan: (int) $validated['bulan'], tahun: (int) $validated['tahun'], nilaiBulanIni: (int) $validated['nilai'], idLevelBulanIni: (int) $validated['id_level']);

        TopMonitoringBulanan::query()->create([
            'id_risiko' => $topRisk->id_risiko,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'nilai' => $validated['nilai'],
            'id_level' => $validated['id_level'],
            'status' => $validated['status'],
            'progres_belum' => $validated['progres_belum'] ?? 0,
            'progres_proses' => $validated['progres_proses'] ?? 0,
            'progres_sudah' => $validated['progres_sudah'] ?? 0,
            'id_aturan_efektivitas' => $idAturanEfektivitas,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil ditambahkan.');
    }

    public function updateMonitoring(Request $request, TopRisiko $topRisk, TopMonitoringBulanan $monitoring): RedirectResponse
    {
        abort_if($monitoring->id_risiko !== $topRisk->id_risiko, 404);

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12', Rule::unique('top_monitoring_bulanan', 'bulan')->where('tahun', $request->integer('tahun'))->where('id_risiko', $topRisk->id_risiko)->ignore($monitoring->id_monitoring, 'id_monitoring')],
            'tahun' => ['required', 'integer', 'digits:4', 'min:2000'],
            'nilai' => ['required', 'integer', 'min:0'],
            'id_level' => ['required', 'integer', 'exists:top_level_risiko,id_level'],
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'progres_belum' => ['nullable', 'integer', 'min:0'],
            'progres_proses' => ['nullable', 'integer', 'min:0'],
            'progres_sudah' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(idRisiko: $topRisk->id_risiko, bulan: (int) $validated['bulan'], tahun: (int) $validated['tahun'], nilaiBulanIni: (int) $validated['nilai'], idLevelBulanIni: (int) $validated['id_level']);

        TopMonitoringBulanan::query()
            ->where('id_monitoring', $monitoring->id_monitoring)
            ->update([
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
                'nilai' => $validated['nilai'],
                'id_level' => $validated['id_level'],
                'status' => $validated['status'],
                'progres_belum' => $validated['progres_belum'] ?? 0,
                'progres_proses' => $validated['progres_proses'] ?? 0,
                'progres_sudah' => $validated['progres_sudah'] ?? 0,
                'id_aturan_efektivitas' => $idAturanEfektivitas,
                'catatan' => $validated['catatan'] ?? null,
            ]);

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil diperbarui.');
    }

    public function destroyMonitoring(TopRisiko $topRisk, TopMonitoringBulanan $monitoring): RedirectResponse
    {
        abort_if($monitoring->id_risiko !== $topRisk->id_risiko, 404);

        TopMonitoringBulanan::query()->where('id_monitoring', $monitoring->id_monitoring)->delete();

        return redirect()->route('top-risk.show', $topRisk)->with('success', 'Data monitoring bulanan berhasil dihapus.');
    }

    private function resolveAturanEfektivitasId(int $idRisiko, int $bulan, int $tahun, int $nilaiBulanIni, int $idLevelBulanIni): ?int
    {
        $periodeSekarang = sprintf('%04d-%02d-01', $tahun, $bulan);

        $monitoringSebelumnya = TopMonitoringBulanan::query()
            ->with('level')
            ->where('id_risiko', $idRisiko)
            ->whereRaw("STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') < ?", [$periodeSekarang])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        if ($monitoringSebelumnya === null) {
            return null;
        }

        $levelBulanIni = TopLevelRisiko::query()->where('id_level', $idLevelBulanIni)->firstOrFail();

        $kondisiNilai = $this->compareValue(current: $nilaiBulanIni, previous: $monitoringSebelumnya->nilai);

        $kondisiLevel = $this->compareValue(current: $levelBulanIni->urutan, previous: $monitoringSebelumnya->level->urutan);

        return TopAturanEfektivitas::query()->where('kondisi_nilai', $kondisiNilai)->where('kondisi_level', $kondisiLevel)->value('id_aturan');
    }

    private function compareValue(int $current, int $previous): string
    {
        if ($current < $previous) {
            return '<';
        }

        if ($current > $previous) {
            return '>';
        }

        return '=';
    }
}
