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

    /**
     * Menentukan ID Level berdasarkan skor risiko.
     */
    public function determineLevelId(int $score): int
    {
        return match (true) {
            $score >= 1  && $score <= 5  => 1,
            $score >= 6  && $score <= 11 => 2,
            $score >= 12 && $score <= 15 => 3,
            $score >= 16 && $score <= 19 => 4,
            $score >= 20 && $score <= 25 => 5,
            default                      => 1,
        };
    }

    /**
     * Menghitung tren risiko (Naik, Turun, Stabil).
     */
    public function calculateTrend(int $currentScore, int $inherentScore): string
    {
        return match (true) {
            $currentScore > $inherentScore => 'Naik',
            $currentScore < $inherentScore => 'Turun',
            default                        => 'Stabil',
        };
    }

    /**
     * Menghitung tingkat efektivitas penanganan risiko.
     */
    public function calculateEfektivitas(int $currentScore, int $inherentScore): string
    {
        $levelCurrentId  = $this->determineLevelId($currentScore);
        $levelInherentId = $this->determineLevelId($inherentScore);

        $isLevelAman      = in_array($levelCurrentId, [1, 2], true);
        $isLevelBelumAman = in_array($levelCurrentId, [3, 4, 5], true);

        if ($currentScore === $inherentScore && $isLevelAman) {
            return 'Pencatatan';
        }

        if ($currentScore < $inherentScore && $isLevelAman) {
            return 'Effective';
        }

        if ($currentScore < $inherentScore && $isLevelBelumAman) {
            if ($levelCurrentId < $levelInherentId) {
                return 'Mostly Effective';
            }
            if ($levelCurrentId === $levelInherentId) {
                return 'Partially Effective';
            }
        }

        if ($currentScore === $inherentScore && $isLevelBelumAman) {
            return 'In-Effective';
        }

        return 'Unmeasurable';
    }

    /**
     * Membangun payload data lengkap untuk Dashboard SMAP.
     */
    public function buildDashboardData($selectedPeriode, ?int $selectedYear): array
    {
        // ------------------------------------------------------------------
        // 1. INSISIALISASI TAHUN & PERIODE LOOKUP
        // ------------------------------------------------------------------
        if (!$selectedYear) {
            $latestData   = $this->smapRepo->getLatestPeriodYear();
            $selectedYear = $latestData ? (int) $latestData->year : (int) date('Y');
        }

        $allUnits      = $this->smapRepo->getAllUnits();
        $allLevels     = $this->smapRepo->getAllLevels();
        $allCategories = $this->smapRepo->getSmapCategories();

        $isAllPeriode = ($selectedPeriode === 'all' || empty($selectedPeriode));

        if ($isAllPeriode) {
            $quarterLookups = ['TW1', 'TW2', 'TW3', 'TW4', 1, 2, 3, 4, '1', '2', '3', '4', 'Q1', 'Q2', 'Q3', 'Q4'];
            $periodText     = "Semua Triwulan - {$selectedYear}";
        } else {
            $quarterLookups = ['TW' . $selectedPeriode, $selectedPeriode, (string) $selectedPeriode, 'Q' . $selectedPeriode];
            $periodText     = "Triwulan {$selectedPeriode} - {$selectedYear}";
        }

        $metrics = $this->smapRepo->getDashboardMetrics($quarterLookups, $selectedYear);

        // ------------------------------------------------------------------
        // 2. MAPPING TREN RISIKO
        // ------------------------------------------------------------------
        $trendData = [
            (int) ($metrics['risksPerTrend']['naik']   ?? $metrics['risksPerTrend']['Naik']   ?? 0),
            (int) ($metrics['risksPerTrend']['turun']  ?? $metrics['risksPerTrend']['Turun']  ?? 0),
            (int) ($metrics['risksPerTrend']['stabil'] ?? $metrics['risksPerTrend']['Stabil'] ?? $metrics['risksPerTrend']['stagnan'] ?? $metrics['risksPerTrend']['Stagnan'] ?? 0),
        ];

        // ------------------------------------------------------------------
        // 3. STACKED CHARTS & UNIT RISKS
        // ------------------------------------------------------------------
        $colorMapping = [
            'High'             => '#FF0100',
            'Moderate to High' => '#FFC000',
            'Moderate'         => '#FFFF00',
            'Low to Moderate'  => '#91D050',
            'Low'              => '#03B050',
        ];

        $stackedTemplates = [];
        foreach ($allLevels as $level) {
            $stackedTemplates[$level->id_level] = [
                'label'           => $level->nama_level,
                'backgroundColor' => $colorMapping[$level->nama_level] ?? '#cbd5e1',
                'data'            => [],
            ];
        }

        $data   = [];
        $labels = [];

        foreach ($allUnits as $unit) {
            $totalUnitRisks = $metrics['risksPerDept'][$unit->id_unit] ?? 0;

            if ($totalUnitRisks > 0) {
                $data[]   = $totalUnitRisks;
                $labels[] = $unit->nama_unit;

                $currentDeptRisks = $this->smapRepo->getDeptRisksPerLevel($quarterLookups, $selectedYear, $unit->id_unit);
                foreach ($allLevels as $level) {
                    $stackedTemplates[$level->id_level]['data'][] = (int) ($currentDeptRisks[$level->id_level] ?? 0);
                }
            }
        }

        // ------------------------------------------------------------------
        // 4. DISTRIBUSI LEVEL
        // ------------------------------------------------------------------
        $levelDistributionData = [];
        $maxLevelCount         = 0;

        foreach ($allLevels as $level) {
            $count                 = $metrics['risksPerLevel'][$level->id_level] ?? 0;
            $maxLevelCount         = max($maxLevelCount, $count);
            $levelDistributionData[] = [
                'name'  => $level->nama_level,
                'count' => $count,
            ];
        }

        foreach ($levelDistributionData as &$item) {
            $item['percentage'] = $maxLevelCount > 0 ? ($item['count'] / $maxLevelCount) * 100 : 0;
        }
        unset($item); // Sanitisasi reference variable

        // ------------------------------------------------------------------
        // 5. KATEGORI SMAP CHARTS
        // ------------------------------------------------------------------
        $catLabels = [];
        $catData   = [];

        foreach ($allCategories as $category) {
            $catLabels[] = $category->nama_kategori;
            $catData[]   = $metrics['risksPerCategory'][$category->id_kategori] ?? 0;
        }

        // ------------------------------------------------------------------
        // 6. PIE CHARTS (Inherent, Current, Target, Progress, Efektif)
        // ------------------------------------------------------------------
        $baseArray = array_fill_keys($allLevels->pluck('id_level')->toArray(), 0);

        // Pie Inherent
        $pieInherent = $baseArray;
        foreach ($this->smapRepo->getMasterDataCountsByYear($selectedYear, 'id_level') as $lvl => $tot) {
            if ($lvl) {
                $pieInherent[$lvl] = (int) $tot;
            }
        }

        // Pie Current
        $pieCurrent = $baseArray;
        foreach ($metrics['risksPerLevel'] as $lvl => $tot) {
            if ($lvl) {
                $pieCurrent[$lvl] = (int) $tot;
            }
        }

        // Pie Target
        $pieTarget = $baseArray;
        foreach ($this->smapRepo->getMasterDataCountsByYear($selectedYear, 'id_level_target') as $lvl => $tot) {
            if ($lvl) {
                $pieTarget[$lvl] = (int) $tot;
            }
        }

        // Base Progres
        $baseProgres = [
            'belum'          => 0,
            'proses'         => 0,
            'sudah'          => 0,
            'progress_sudah' => 0,
        ];

        foreach ($this->smapRepo->getProgresOffData($selectedYear, $quarterLookups) as $status => $tot) {
            $key = strtolower($status ?: 'belum');

            if ($key === 'progress_sudah' || $key === 'selesai') {
                $key = 'sudah';
            }

            if (array_key_exists($key, $baseProgres)) {
                $baseProgres[$key] = (int) $tot;
            }
        }

        // Base Efektif
        $baseEfektif = [
            'Pencatatan'          => 0,
            'Effective'           => 0,
            'Mostly Effective'    => 0,
            'Partially Effective' => 0,
            'In-Effective'        => 0,
            'Unmeasurable'        => 0,
        ];

        foreach ($this->smapRepo->getEfektifOffData($selectedYear, $quarterLookups) as $status => $tot) {
            if ($status && array_key_exists($status, $baseEfektif)) {
                $baseEfektif[$status] = (int) $tot;
            }
        }

        // Construct Pie Structure
        $smapPieData = [
            'labels'   => array_values($allLevels->pluck('nama_level')->toArray()),
            'inherent' => array_values($pieInherent),
            'current'  => array_values($pieCurrent),
            'target'   => array_values($pieTarget),
            'progres'  => [
                'labels' => ['Belum Dimulai', 'Sedang Berjalan', 'Selesai'],
                'off'    => array_values($baseProgres),
            ],
            'efektif'  => [
                'labels' => array_keys($baseEfektif),
                'off'    => array_values($baseEfektif),
            ],
        ];

        // ------------------------------------------------------------------
        // 7. RETURN PAYLOAD UNTUK CONTROLLER & BLADE
        // ------------------------------------------------------------------
        $smapUnitTable     = $this->smapRepo->getUnitProgressTableData($selectedYear, $quarterLookups);
        $totalSudahProgres = (int) ($baseProgres['sudah'] ?? $baseProgres['progress_sudah'] ?? 0);

        return [
            'selectedPeriode' => $selectedPeriode,
            'selectedYear'    => $selectedYear,
            'summary'         => [
                'total_risiko'      => $metrics['totalRisiko'],
                'risiko_aktif'      => $metrics['risikoAktif'],
                'jumlah_departemen' => $allUnits->count(),
                'progress_sudah'    => $totalSudahProgres,
                'sudah_progres'     => $totalSudahProgres,
                'selesai'           => $totalSudahProgres,
            ],
            'periodText'         => $periodText,
            'labels'             => array_values($labels),
            'data'               => array_values($data),
            'chartDatasets'      => array_values($stackedTemplates),
            'catLabels'          => array_values($catLabels),
            'catData'            => array_values($catData),
            'trendLabels'        => ['Naik', 'Turun', 'Stagnan'],
            'trendData'          => $trendData,
            'smapPieData'        => $smapPieData,
            'smapUnitTable'      => $smapUnitTable,
            'level_distribution' => $levelDistributionData,
        ];
    }
}
