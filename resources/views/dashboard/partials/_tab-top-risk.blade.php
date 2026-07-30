<div id="content-top_risk" class="tab-content hidden space-y-6 animate-fade-in-up">

    @php
        $currentYear = (int) date('Y');
        // Default ke 0 (Semua Bulan) jika tidak ada request 'bulan'
        $selectedBulan = (int) request('bulan', 0);
        $selectedTahun = (int) request('tahun', $currentYear);

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periodeLabelText = ($selectedBulan > 0 ? ($monthNames[$selectedBulan] ?? '') : 'Semua Bulan') . ' ' . $selectedTahun;

        // Summary data dengan fallback
        $summary = $summary ?? ['total_risiko' => 0, 'risiko_aktif' => 0, 'rata_rata_nilai' => 0, 'tren' => 'Stagnan'];
        $period = $period ?? ['label' => $periodeLabelText, 'previous_label' => 'Periode Sebelumnya'];
    @endphp

    <!-- 1. Header Judul Tab Top Risk -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Analisis Top Risk</h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Monitoring Risiko Tingkat Tinggi per Kategori & Unit Kerja
            </p>
        </div>
    </div>

    <!-- 2. Filter Form -->
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="top_risk">

            <div class="lg:col-span-3">
                <label for="bulan_top" class="block text-sm font-semibold text-slate-700">
                    Bulan Monitoring
                </label>
                <select id="bulan_top" name="bulan" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="0" @selected($selectedBulan === 0)>Semua Bulan</option>
                    @foreach ($monthNames as $m => $name)
                        <option value="{{ $m }}" @selected($selectedBulan === $m)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="tahun_top" class="block text-sm font-semibold text-slate-700">Tahun</label>
                <input id="tahun_top" type="number" name="tahun" value="{{ $selectedTahun }}" min="2000" max="{{ $currentYear + 5 }}" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-150 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 lg:w-auto">
                    Tampilkan Dashboard Top Risk
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Summary Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Card Total --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-slate-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-slate-400 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #475569 !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Top Risk</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($summary['total_risiko'] ?? 0) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Seluruh kategori risiko</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #475569 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(71, 85, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-slate-600 border-t-2 border-slate-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #475569 !important;"></span>
                <span>Akumulasi Top Risk</span>
            </div>
        </div>

        {{-- Card Aktif --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-blue-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #2563eb !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Risiko Aktif</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($summary['risiko_aktif'] ?? 0) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Dalam proses penanganan</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-blue-700 border-t-2 border-blue-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #2563eb !important;"></span>
                <span>Sedang Ditangani</span>
            </div>
        </div>

        {{-- Card Rata-Rata Nilai --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-amber-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-amber-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #d97706 !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-900">Rata-Rata Nilai</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($summary['rata_rata_nilai'] ?? 0, 1) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Skor dampak & kemungkinan</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #d97706 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(217, 119, 6, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-amber-700 border-t-2 border-amber-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #d97706 !important;"></span>
                <span>Tren: {{ $summary['tren'] ?? 'Stagnan' }}</span>
            </div>
        </div>
    </div>

    {{-- Sub-partials chart Top Risk --}}
    @includeIf('top-risk.partials._chart-nilai', [
        'nilaiTopRisk' => $nilaiTopRisk ?? []
    ])

    @includeIf('top-risk.partials._chart-unit-level', [
        'unitLevelDistribution' => $unitLevelDistribution ?? ['labels' => [], 'datasets' => []]
    ])

    @includeIf('top-risk.partials._heatmap-risk', [
        'heatmap' => $heatmap ?? ['rows' => [], 'risks' => []]
    ])

    {{-- 1. Container Grid 2 Kolom: Distribusi Level Risiko & Kategori Risiko --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @includeIf('top-risk.partials._level-distribution', [
            'items' => $levelDistributionItems ?? []
        ])

        @includeIf('top-risk.partials._category-distribution', [
            'items' => $categoryDistributionItems ?? []
        ])
    </div>

    {{-- 2. Container Grid 2 Kolom: Progres & Efektivitas Penanganan Risiko (Langsung di Bawahnya) --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @includeIf('top-risk.partials._chart-progress', [
            'progressDistribution' => $progressDistribution ?? ['labels' => ['Belum', 'Proses', 'Sudah'], 'data' => [0, 0, 0], 'colors' => ['#FCD34D', '#A3E635', '#93C5FD']]
        ])

        @includeIf('top-risk.partials._chart-effectiveness', [
            'effectivenessDistribution' => $effectivenessDistribution ?? ['labels' => ['Belum Dinilai'], 'data' => [0], 'colors' => ['#bbf7d0']]
        ])
    </div>

    {{-- 3. Grafik Tren --}}
    @includeIf('top-risk.partials._trend-risk', [
        'items' => $trendRiskItems ?? [],
        'period' => $period
    ])

</div>