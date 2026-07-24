<div id="content-smap" class="tab-content hidden space-y-6 animate-fade-in-up">

    @php
        $selectedPeriode = $selectedPeriode ?? 'all';
    @endphp

    <!-- 1. Header Judul Tab SMAP -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Analisis Risiko SMAP (Kepatuhan)</h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Monitoring Sistem Manajemen Anti Penyuapan & Metrik Risiko Kepatuhan
            </p>
        </div>
    </div>

    <!-- 2. Filter Form khusus Tab SMAP -->
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="smap">

            <div class="lg:col-span-3">
                <label for="periode" class="block text-sm font-semibold text-slate-700">
                    Periode (Triwulan)
                </label>
                <select id="periode" name="periode" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="all" @selected((string)$selectedPeriode === 'all')>Semua Triwulan</option>
                    @foreach ([1 => 'Triwulan I', 2 => 'Triwulan II', 3 => 'Triwulan III', 4 => 'Triwulan IV'] as $periodeNumber => $periodeName)
                        <option value="{{ $periodeNumber }}" @selected((string)$selectedPeriode === (string)$periodeNumber)>
                            {{ $periodeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="tahun" class="block text-sm font-semibold text-slate-700">Tahun</label>
                <input id="tahun" type="number" name="tahun" value="{{ $selectedYear ?? date('Y') }}" min="2000" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-150 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 lg:w-auto">
                    Tampilkan Dashboard SMAP
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Dynamic Summary Metric Cards (Gaya Disamakan dengan Departemen) -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        {{-- Card 1: Total Risiko SMAP --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-slate-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-slate-400 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #6b21a8 !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-900">Total Risiko SMAP</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($dashboardData['summary']['total_risiko'] ?? ($summary['smap'] ?? 0)) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Terdaftar dalam sistem</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #6b21a8 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(107, 33, 168, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-purple-800 border-t-2 border-purple-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #6b21a8 !important;"></span>
                <span>Sistem Manajemen Anti Penyuapan</span>
            </div>
        </div>

        {{-- Card 2: Risiko Aktif --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-indigo-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #4338ca !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-900">Risiko Aktif</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($dashboardData['summary']['risiko_aktif'] ?? 0) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Dalam pantauan periode</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #4338ca !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(67, 56, 202, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-indigo-700 border-t-2 border-indigo-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #4338ca !important;"></span>
                <span>Monitoring Aktif Terpantau</span>
            </div>
        </div>

        {{-- Card 3: Mitigasi Selesai --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-emerald-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #059669 !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Selesai / Sudah Progres</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($dashboardData['summary']['mitigasi_selesai'] ?? 0) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Tindakan pencegahan terlaksana</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-700 border-t-2 border-emerald-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #059669 !important;"></span>
                <span>Telah Termitigasi</span>
            </div>
        </div>

    </div>

    <!-- 4. Sub-partials Chart & Tabel SMAP -->
    @include('smap.partials._list-departement')
    @include('smap.partials._chart-pie-risiko')
    @include('smap.partials._chart-pie-efektif')
    @include('smap.partials._chart-pie-penanganan')

</div>
