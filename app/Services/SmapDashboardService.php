<?php

namespace App\Services;

use App\Repositories\SmapRepository;

class SmapDashboardService
{
    public function __construct(
        protected SmapRepository $smapRepo,
        protected SmapService $smapService
    ) {}

    public function getSmapDashboardData(string $periode = 'all', ?int $tahun = null): array
    {
        // 1. Tentukan Tahun & Quarter sesuai logika SmapRepository
        $selectedYear = $tahun ?? date('Y');

        // Konversi periode ('all', '1', '2', dll) menjadi array quarter
        if ($periode === 'all' || empty($periode)) {
            $quarterLookups = [1, 2, 3, 4];
        } else {
            $quarterLookups = [(int) $periode];
        }

        // 2. Ambil data asli langsung dari SmapService
        $data = $this->smapService->buildDashboardData($periode, $selectedYear);

        // 3. Ambil data Progress Per Unit langsung dari method teruji di SmapRepository!
        $unitProgressTable = $this->smapRepo->getUnitProgressTableData($selectedYear, $quarterLookups);

        // 4. Bungkus ke variabel dashboardData
        $data['dashboardData'] = [
            'summary'               => $data['summary'] ?? [],
            'period'                => $data['periodText'] ?? '',
            'level_distribution'    => $data['level_distribution'] ?? [],
            'trend_risk'            => $data['trendData'] ?? [],
            'category_distribution' => $data['catData'] ?? [],
            'unit_progress'         => $unitProgressTable, // ⬅️ Menggunakan query asli SmapRepository
            'heatmap'               => [],
            'status_distribution'   => [],
        ];

        return $data;
    }
}
