<?php

namespace App\Services;

use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\TopMonitoringBulanan;
use App\Models\TopRisiko;
use App\Models\TopUnitKerja;
use Illuminate\Support\Collection;

class TopRiskDashboardService
{
    public function buildTopRiskDashboardData(int $selectedMonth, int $selectedYear): array
    {
        // 1. Ambil Data Current Monitoring
        $currentQuery = TopMonitoringBulanan::query()
            ->with(['risiko.kategori', 'risiko.unitKerja', 'level', 'aturanEfektivitas'])
            ->where('tahun', $selectedYear);

        if ($selectedMonth > 0) {
            $currentQuery->where('bulan', $selectedMonth);
        } else {
            // PERBAIKAN 1: Ambil berdasarkan MAX(bulan) tiap risiko, bukan MAX(id_monitoring)
            $currentQuery->whereIn('id_monitoring', function ($subQuery) use ($selectedYear) {
                $subQuery->selectRaw('t1.id_monitoring')
                    ->from('top_monitoring_bulanan as t1')
                    ->where('t1.tahun', $selectedYear)
                    ->whereRaw('t1.bulan = (SELECT MAX(t2.bulan) FROM top_monitoring_bulanan t2 WHERE t2.id_risiko = t1.id_risiko AND t2.tahun = ?)', [$selectedYear]);
            });
        }
        $currentMonitoring = $currentQuery->get();

        // 2. Ambil Data Previous Monitoring
        [$previousMonth, $previousYear] = $this->resolvePreviousPeriod($selectedMonth, $selectedYear);

        $previousQuery = TopMonitoringBulanan::query()->where('tahun', $previousYear);

        if ($previousMonth > 0) {
            $previousQuery->where('bulan', $previousMonth);
        } else {
            $previousQuery->whereIn('id_monitoring', function ($subQuery) use ($previousYear) {
                $subQuery->selectRaw('t1.id_monitoring')
                    ->from('top_monitoring_bulanan as t1')
                    ->where('t1.tahun', $previousYear)
                    ->whereRaw('t1.bulan = (SELECT MAX(t2.bulan) FROM top_monitoring_bulanan t2 WHERE t2.id_risiko = t1.id_risiko AND t2.tahun = ?)', [$previousYear]);
            });
        }
        $previousMonitoring = $previousQuery->get();

        // 3. Hitung Rata-rata & Label
        $averageCurrentValue = round((float) ($currentMonitoring->avg('nilai') ?? 0), 2);
        $averagePreviousValue = round((float) ($previousMonitoring->avg('nilai') ?? 0), 2);

        $currentLabel = $selectedMonth > 0 ? $this->monthName($selectedMonth).' '.$selectedYear : 'Tahun '.$selectedYear;
        $previousLabel = $previousMonth > 0 ? $this->monthName($previousMonth).' '.$previousYear : 'Tahun '.$previousYear;

        return [
            'period' => [
                'month' => $selectedMonth,
                'year' => $selectedYear,
                'label' => $currentLabel,
                'previous_label' => $previousLabel,
            ],
            'summary' => [
                'total_risiko' => TopRisiko::query()->count('*'),
                'risiko_aktif' => TopRisiko::query()->where('is_aktif', true)->count('*'),
                'rata_rata_nilai' => $averageCurrentValue,
                'tren' => $this->resolveTwoPeriodTrendLabel($averageCurrentValue, $averagePreviousValue),
                'total_monitoring' => $currentMonitoring->count(),
            ],
            'heatmap' => $this->buildHeatmapData($currentMonitoring),
            'level_distribution' => $this->buildLevelDistribution($currentMonitoring),
            'category_distribution' => $this->buildCategoryDistribution($currentMonitoring),
            'status_distribution' => $this->buildStatusDistribution($currentMonitoring),
            'trend_risk' => $this->buildRiskTrendRows($currentMonitoring, $selectedMonth, $selectedYear),
            'unit_level_distribution' => $this->buildUnitLevelDistribution($currentMonitoring),
            'progress_distribution' => $this->buildProgressDistribution($currentMonitoring),
            'effectiveness_distribution' => $this->buildEffectivenessDistribution($currentMonitoring),
        ];
    }

    private function buildHeatmapData(Collection $currentMonitoring): array
    {
        $monitoringRows = $currentMonitoring->sortByDesc('nilai')->values()
            ->map(function (TopMonitoringBulanan $monitoring, int $index): array {
                return [
                    'code' => 'R'.($index + 1),
                    'risk_name' => $monitoring->risiko?->nama_peristiwa_risiko ?? '-',
                    'value' => (int) $monitoring->nilai,
                    'level' => $monitoring->level?->nama_level ?? '-',
                ];
            });

        $cells = collect(range(25, 1))
            ->map(function (int $value) use ($monitoringRows): array {
                return [
                    'value' => $value,
                    'risks' => $monitoringRows->where('value', $value)->values(),
                    'class' => $this->resolveHeatmapCellClass($value),
                ];
            })->chunk(5)->values()->all();

        return ['rows' => $cells, 'risks' => $monitoringRows];
    }

    private function resolveHeatmapCellClass(int $value): string
    {
        if ($value >= 21) { return 'bg-rose-100 text-rose-900 ring-rose-200'; }
        if ($value >= 16) { return 'bg-orange-100 text-orange-900 ring-orange-200'; }
        if ($value >= 11) { return 'bg-amber-100 text-amber-900 ring-amber-200'; }
        if ($value >= 6) { return 'bg-lime-100 text-lime-900 ring-lime-200'; }
        return 'bg-emerald-100 text-emerald-900 ring-emerald-200';
    }

    private function buildLevelDistribution(Collection $currentMonitoring): Collection
    {
        return LevelRisiko::query()->orderBy('urutan', 'asc')->get()
            ->map(function (LevelRisiko $levelRisiko) use ($currentMonitoring): array {
                return [
                    'label' => $levelRisiko->nama_level,
                    'total' => $currentMonitoring->where('id_level', $levelRisiko->id_level)->count(),
                    'color' => $levelRisiko->kode_warna,
                ];
            });
    }

    private function buildCategoryDistribution(Collection $currentMonitoring): Collection
    {
        return KategoriRisiko::query()->orderBy('nama_kategori', 'asc')->get()
            ->map(function (KategoriRisiko $kategoriRisiko) use ($currentMonitoring): array {
                $total = $currentMonitoring->filter(function ($monitoring) use ($kategoriRisiko): bool {
                    return (int) ($monitoring->risiko?->id_kategori ?? 0) === (int) $kategoriRisiko->id_kategori;
                })->count();
                return ['label' => $kategoriRisiko->nama_kategori, 'total' => $total];
            });
    }

    private function buildStatusDistribution(Collection $currentMonitoring): Collection
    {
        return collect([
            ['label' => 'Aktif', 'total' => $currentMonitoring->where('status', 'Aktif')->count()],
            ['label' => 'Tidak Aktif', 'total' => $currentMonitoring->where('status', 'Tidak Aktif')->count()],
        ]);
    }

    private function buildRiskTrendRows(Collection $currentMonitoring, int $selectedMonth, int $selectedYear): Collection
    {
        return $currentMonitoring->sortByDesc('nilai')->values()
            ->map(function (TopMonitoringBulanan $monitoring, int $index) use ($selectedMonth, $selectedYear): array {
                $trendAnalysis = $this->resolveRiskTrendAnalysis(
                    (int) $monitoring->id_risiko, $selectedMonth, $selectedYear
                );
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
        $query = TopMonitoringBulanan::query()->where('id_risiko', $idRisiko);

        // PERBAIKAN 2: Handling pencarian riwayat trend yang aman untuk 'Semua Bulan' (selectedMonth = 0)
        if ($selectedMonth > 0) {
            $selectedPeriod = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
            $query->whereRaw("STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') <= ?", [$selectedPeriod]);
        } else {
            $query->where('tahun', '<=', $selectedYear);
        }

        $monitoringValues = $query->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get(['bulan', 'tahun', 'nilai'])
            ->map(function ($monitoring): array {
                return [
                    'label' => $this->shortMonthName((int) $monitoring->bulan).' '.$monitoring->tahun,
                    'value' => (int) $monitoring->nilai
                ];
            })->values();

        if ($monitoringValues->count() <= 1) {
            return [
                'trend' => 'Belum ada pembanding',
                'description' => 'Belum tersedia data bulan sebelumnya sebagai pembanding.',
                'values' => $monitoringValues
            ];
        }

        $firstValue = (int) $monitoringValues->first()['value'];
        $lastValue = (int) $monitoringValues->last()['value'];
        $hasIncrease = false;
        $hasDecrease = false;

        for ($index = 1; $index < $monitoringValues->count(); $index++) {
            $prev = (int) $monitoringValues->get($index - 1)['value'];
            $curr = (int) $monitoringValues->get($index)['value'];
            if ($curr > $prev) $hasIncrease = true;
            if ($curr < $prev) $hasDecrease = true;
        }

        if (! $hasIncrease && ! $hasDecrease) return ['trend' => 'Stagnan', 'description' => 'Semua nilai risiko sama pada seluruh periode monitoring.', 'values' => $monitoringValues];
        if (! $hasDecrease && $lastValue > $firstValue) return ['trend' => 'Naik', 'description' => 'Nilai risiko tidak pernah turun dan nilai akhir lebih besar dari nilai awal.', 'values' => $monitoringValues];
        if (! $hasIncrease && $lastValue < $firstValue) return ['trend' => 'Turun', 'description' => 'Nilai risiko tidak pernah naik dan nilai akhir lebih kecil dari nilai awal.', 'values' => $monitoringValues];
        return ['trend' => 'Fluktuatif', 'description' => 'Pola nilai risiko campuran, terdapat perubahan naik dan turun antar bulan.', 'values' => $monitoringValues];
    }

    private function resolvePreviousPeriod(int $selectedMonth, int $selectedYear): array
    {
        if ($selectedMonth === 0) {
            return [0, $selectedYear - 1];
        }
        return $selectedMonth === 1 ? [12, $selectedYear - 1] : [$selectedMonth - 1, $selectedYear];
    }

    private function resolveTwoPeriodTrendLabel(float $currentValue, float $previousValue): string
    {
        if ($currentValue > $previousValue) return 'Naik';
        if ($currentValue < $previousValue) return 'Turun';
        return 'Stagnan';
    }

    private function shortMonthName(int $month): string
    {
        return match ($month) { 1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des', default=>'-' };
    }

    private function monthName(int $month): string
    {
        return match ($month) { 1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember', default=>'-' };
    }

    private function buildUnitLevelDistribution(Collection $currentMonitoring): array
    {
        $units = TopUnitKerja::query()->orderBy('nama_unit', 'asc')->get();
        $levels = LevelRisiko::query()->orderBy('urutan', 'desc')->get();

        $labels = [];
        $datasets = [];

        foreach ($levels as $level) {
            $datasets[$level->id_level] = [
                'label' => $level->nama_level,
                'data' => [],
                'backgroundColor' => $level->kode_warna ?? '#cbd5e1',
                'borderWidth' => 1,
                'borderColor' => '#334155'
            ];
        }

        foreach ($units as $unit) {
            $labels[] = $unit->nama_unit;
            $monitoringsForUnit = $currentMonitoring->filter(function ($monitoring) use ($unit): bool {
                return $monitoring->risiko !== null
                    && $monitoring->risiko->unitKerja->contains('id_unit', $unit->id_unit);
            });
            $countsByLevel = $monitoringsForUnit->countBy('id_level');

            foreach ($levels as $level) {
                $datasets[$level->id_level]['data'][] = $countsByLevel->get($level->id_level, 0);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => array_values($datasets),
        ];
    }

    private function buildProgressDistribution(Collection $currentMonitoring): array
    {
        $belum = (int) $currentMonitoring->sum('progres_belum');
        $proses = (int) $currentMonitoring->sum('progres_proses');
        $sudah = (int) $currentMonitoring->sum('progres_sudah');

        if ($belum === 0 && $proses === 0 && $sudah === 0) {
            return [
                'labels' => ['Belum', 'Proses', 'Sudah'],
                'data' => [1, 0, 0],
                'colors' => ['#FCD34D', '#A3E635', '#93C5FD']
            ];
        }

        return [
            'labels' => ['Belum', 'Proses', 'Sudah'],
            'data' => [$belum, $proses, $sudah],
            'colors' => ['#FCD34D', '#A3E635', '#93C5FD']
        ];
    }

    private function buildEffectivenessDistribution(Collection $currentMonitoring): array
    {
        $stats = $currentMonitoring->groupBy('aturanEfektivitas.hasil')
            ->map(function ($items, $key) {
                return [
                    'label' => !empty($key) ? $key : 'Belum Dinilai',
                    'count' => $items->count()
                ];
            });

        if ($stats->isEmpty()) {
            return [
                'labels' => ['Belum Dinilai'],
                'data' => [1],
                'colors' => ['#bbf7d0']
            ];
        }

        return [
            'labels' => $stats->pluck('label')->toArray(),
            'data' => $stats->pluck('count')->toArray(),
            'colors' => ['#bbf7d0', '#f87171', '#fbbf24', '#94a3b8']
        ];
    }
}
