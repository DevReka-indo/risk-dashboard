<?php

namespace App\Services;

use App\Repositories\SmapRepository;


class SmapService
{
    protected SmapRepository $smapRepo;

    public function __construct(SmapRepository $smapRepo)
    {
        $this->smapRepo = $smapRepo;
    }

    public function determineLevelId(int $score): int
    {
        if ($score >= 1 && $score <= 5)   return 1;
        if ($score >= 6 && $score <= 11)  return 2;
        if ($score >= 12 && $score <= 15) return 3;
        if ($score >= 16 && $score <= 19) return 4;
        if ($score >= 20 && $score <= 25) return 5;
        return 1;
    }

    public function calculateTrend(int $currentScore, int $inherentScore): string
    {
        if ($currentScore > $inherentScore) return 'Naik';
        if ($currentScore < $inherentScore) return 'Turun';
        return 'Stabil';
    }

    public function calculateEfektivitas(int $currentScore, int $inherentScore): string
    {
        $levelCurrentId  = $this->determineLevelId($currentScore);
        $levelInherentId = $this->determineLevelId($inherentScore);

        $isLevelAman      = in_array($levelCurrentId, [1, 2]);
        $isLevelBelumAman = in_array($levelCurrentId, [3, 4, 5]);

        if ($currentScore === $inherentScore && $isLevelAman) {
            return 'Pencatatan';
        } elseif ($currentScore < $inherentScore && $isLevelAman) {
            return 'Effective';
        } elseif ($currentScore < $inherentScore && $isLevelBelumAman && $levelCurrentId < $levelInherentId) {
            return 'Mostly Effective';
        } elseif ($currentScore < $inherentScore && $isLevelBelumAman && $levelCurrentId === $levelInherentId) {
            return 'Partially Effective';
        } elseif ($currentScore === $inherentScore && $isLevelBelumAman) {
            return 'In-Effective';
        }

        return 'Unmeasurable';
    }

    public function buildDashboardData($selectedPeriode, ?int $selectedYear)
    {
        if (!$selectedYear) {
            $latestData = $this->smapRepo->getLatestPeriodYear();
            $selectedYear = $latestData ? (int)$latestData->year : (int)date('Y');
        }

        $allUnits = $this->smapRepo->getAllUnits();
        $allLevels = $this->smapRepo->getAllLevels();
        $allCategories = $this->smapRepo->getSmapCategories();

        // ⬇️ FIX UTAMA: Penanganan 'all' vs Angka Triwulan Specific
        $isAllPeriode = ($selectedPeriode === 'all' || empty($selectedPeriode));

        if ($isAllPeriode) {
            // Ambil Seluruh Kuartal (TW1 - TW4)
            $quarterLookups = ['TW1', 'TW2', 'TW3', 'TW4', 1, 2, 3, 4, '1', '2', '3', '4', 'Q1', 'Q2', 'Q3', 'Q4'];
            $periodText = "Semua Triwulan - {$selectedYear}";
        } else {
            $stringQuarter = 'TW' . $selectedPeriode;
            $quarterLookups = [$stringQuarter, $selectedPeriode, (string)$selectedPeriode, 'Q' . $selectedPeriode];
            $periodText = "Triwulan {$selectedPeriode} - {$selectedYear}";
        }

        $metrics = $this->smapRepo->getDashboardMetrics($quarterLookups, $selectedYear);

        // Trend Mapping
        $trendData = [
            (int)($metrics['risksPerTrend']['naik'] ?? $metrics['risksPerTrend']['Naik'] ?? 0),
            (int)($metrics['risksPerTrend']['turun'] ?? $metrics['risksPerTrend']['Turun'] ?? 0),
            (int)($metrics['risksPerTrend']['stabil'] ?? $metrics['risksPerTrend']['Stabil'] ?? $metrics['risksPerTrend']['stagnan'] ?? $metrics['risksPerTrend']['Stagnan'] ?? 0),
        ];

        // Stacked Charts
        $stackedTemplates = [];
        $colorMapping = [
            'High'             => '#FF0100',
            'Moderate to High' => '#FFC000',
            'Moderate'         => '#FFFF00',
            'Low to Moderate'  => '#91D050',
            'Low'              => '#03B050'
        ];

        foreach ($allLevels as $level) {
            $stackedTemplates[$level->id_level] = [
                'label' => $level->nama_level,
                'backgroundColor' => $colorMapping[$level->nama_level] ?? '#cbd5e1',
                'data' => []
            ];
        }

        $data = [];
        $labels = [];
        foreach ($allUnits as $unit) {
            $totalUnitRisks = $metrics['risksPerDept'][$unit->id_unit] ?? 0;
            if ($totalUnitRisks > 0) {
                $data[] = $totalUnitRisks;
                $labels[] = $unit->nama_unit;

                $currentDeptRisks = $this->smapRepo->getDeptRisksPerLevel($quarterLookups, $selectedYear, $unit->id_unit);
                foreach ($allLevels as $level) {
                    $stackedTemplates[$level->id_level]['data'][] = (int)($currentDeptRisks[$level->id_level] ?? 0);
                }
            }
        }

        // Level Distribution
        $levelDistributionData = [];
        $maxLevelCount = 0;
        foreach ($allLevels as $level) {
            $count = $metrics['risksPerLevel'][$level->id_level] ?? 0;
            $maxLevelCount = max($maxLevelCount, $count);
            $levelDistributionData[] = ['name' => $level->nama_level, 'count' => $count];
        }
        foreach ($levelDistributionData as &$item) {
            $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
        }

        // Category Charts
        $catLabels = [];
        $catData = [];
        foreach ($allCategories as $category) {
            $catLabels[] = $category->nama_kategori;
            $catData[] = $metrics['risksPerCategory'][$category->id_kategori] ?? 0;
        }

        // Pie charts logic (Inherent, Current, Target, Progress, Efektif)
        $baseArray = array_fill_keys($allLevels->pluck('id_level')->toArray(), 0);

        $pieInherent = $baseArray;
        foreach ($this->smapRepo->getMasterDataCountsByYear($selectedYear, 'id_level') as $lvl => $tot) { if($lvl) $pieInherent[$lvl] = (int)$tot; }

        $pieCurrent = $baseArray;
        foreach ($metrics['risksPerLevel'] as $lvl => $tot) { if($lvl) $pieCurrent[$lvl] = (int)$tot; }

        $pieTarget = $baseArray;
        foreach ($this->smapRepo->getMasterDataCountsByYear($selectedYear, 'id_level_target') as $lvl => $tot) { if($lvl) $pieTarget[$lvl] = (int)$tot; }

        // Progress Pie
        $baseProgres = ['belum' => 0, 'proses' => 0, 'selesai' => 0];
        foreach ($this->smapRepo->getProgresOffData($selectedYear, $quarterLookups) as $status => $tot) {
            $key = strtolower($status ?: 'belum');
            if (array_key_exists($key, $baseProgres)) { $baseProgres[$key] = (int)$tot; }
        }

        // Efektif Pie
        $baseEfektif = ['Pencatatan' => 0, 'Effective' => 0, 'Mostly Effective' => 0, 'Partially Effective' => 0, 'In-Effective' => 0, 'Unmeasurable' => 0];
        foreach ($this->smapRepo->getEfektifOffData($selectedYear, $quarterLookups) as $status => $tot) {
            if ($status && array_key_exists($status, $baseEfektif)) { $baseEfektif[$status] = (int)$tot; }
        }

        $smapPieData = [
            'labels'   => array_values($allLevels->pluck('nama_level')->toArray()),
            'inherent' => array_values($pieInherent),
            'current'  => array_values($pieCurrent),
            'target'   => array_values($pieTarget),
            'progres'  => ['labels' => ['Belum Dimulai', 'Sedang Berjalan', 'Selesai'], 'off' => array_values($baseProgres)],
            'efektif'  => ['labels' => array_keys($baseEfektif), 'off' => array_values($baseEfektif)]
        ];

        // Ambil Data Tabel Progres Unit Kerja
        $smapUnitTable = $this->smapRepo->getUnitProgressTableData($selectedYear, $quarterLookups);

        return [
            'selectedPeriode' => $selectedPeriode, // ⬅️ Masukkan agar Blade tahu periode aktif
            'selectedYear' => $selectedYear,
            'summary' => [
                'total_risiko'      => $metrics['totalRisiko'],
                'risiko_aktif'      => $metrics['risikoAktif'],
                'jumlah_departemen' => $allUnits->count(),
            ],
            'periodText' => $periodText, // ⬅️ Menampilkan "Semua Triwulan - [Tahun]" saat mode all
            'labels' => array_values($labels),
            'data' => array_values($data),
            'chartDatasets' => array_values($stackedTemplates),
            'catLabels' => array_values($catLabels),
            'catData' => array_values($catData),
            'trendLabels' => ['Naik', 'Turun', 'Stagnan'],
            'trendData' => $trendData,
            'smapPieData' => $smapPieData,
            'smapUnitTable' => $smapUnitTable,
            'level_distribution' => $levelDistributionData
        ];
    }
}
