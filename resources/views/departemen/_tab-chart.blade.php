<div class="space-y-6">
    {{-- 1. Bagian Filter Dashboard Manual (Dipisahkan ke dalam partials) --}}
    @include('departemen.partial._filter-dashboard')

    {{-- 2. Kartu Ringkasan --}}
    @include('departemen.partial._summary-cards', ['summary' => $summary, 'period' => $period])

    {{-- 3. GRAFIK DISTRIBUSI DEPARTEMEN --}}
    @include('departemen.partial._departemen-chart', ['chartLabels' => $chartLabels, 'chartData' => $chartData])

    {{-- 4. BAGIAN LEVEL, KATEGORI, TREN, DAN MATRIKS REKAP (Satu Halaman Penuh Lebar ke Bawah) --}}
    <div class="space-y-6 w-full">
        {{-- Distribusi Level Risiko --}}
        @include('departemen.partial._level-distribution', ['items' => $levelDistribution])

        {{-- Jumlah Kategori Risiko --}}
        @include('departemen.partial._category-distribution', ['items' => $categoryDistribution])

        {{-- Trend Nilai Risiko --}}
        @include('departemen.partial._trend-risk', ['items' => $trendRisks])

        {{-- Tabel Matriks Rekapitulasi & Grafik Level Risiko per Departemen --}}
        @include('departemen.partial._matrix-rekap', [
            'rekapUnitLevel' => $rekapUnitLevel,
            'levels' => $levels,
            'matrixChartLabels' => $matrixChartLabels,
            'matrixChartDatasets' => $matrixChartDatasets
        ])
    </div>
</div>
