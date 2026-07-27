<div id="content-dep" class="tab-content hidden space-y-6 animate-fade-in-up">

    @php
        $selectedPeriode = $selectedPeriode ?? 'all';
    @endphp

    <!-- 1. Header Judul Tab Departemen -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Analisis Risiko Departemen</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring risiko operasional per departemen/unit kerja</p>
        </div>
    </div>

    <!-- 2. Filter Form khusus Tab Departemen -->
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('dashboard') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dep">

            <div class="lg:col-span-3">
                <label for="periode_dep" class="block text-sm font-semibold text-slate-700">
                    Periode (Triwulan)
                </label>
                <select id="periode_dep" name="periode" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all" @selected((string)$selectedPeriode === 'all')>Semua Triwulan</option>
                    @foreach ([1 => 'Triwulan I', 2 => 'Triwulan II', 3 => 'Triwulan III', 4 => 'Triwulan IV'] as $periodeNumber => $periodeName)
                        <option value="{{ $periodeNumber }}" @selected((string)$selectedPeriode === (string)$periodeNumber)>
                            {{ $periodeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="tahun_dep" class="block text-sm font-semibold text-slate-700">Tahun</label>
                <input id="tahun_dep" type="number" name="tahun" value="{{ $selectedYear ?? date('Y') }}" min="2000" class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors duration-150 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 lg:w-auto">
                    Tampilkan Dashboard Departemen
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
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Risiko</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_dep']) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Semua unit kerja</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #475569 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(71, 85, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H4.125A1.125 1.125 0 0 0 3 3.375v17.25c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125v-6.375Z" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-slate-600 border-t-2 border-slate-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #475569 !important;"></span>
                <span>Akumulasi Risiko Operasional</span>
            </div>
        </div>

        {{-- Card Aktif --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-blue-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #2563eb !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Aktif</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_dep']) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Dalam proses monitoring</p>
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

        {{-- Card Selesai --}}
        <div class="group relative overflow-hidden rounded-lg border-2 border-emerald-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #059669 !important;"></div>
            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Selesai</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($departemenData['progresData'][2] ?? 0) }}</p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Mitigasi berhasil</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110" style="background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-700 border-t-2 border-emerald-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #059669 !important;"></span>
                <span>Telah Termitigasi</span>
            </div>
        </div>
    </div>

    {{-- Sub-partials chart departemen --}}
    @include('departemen.partial._departemen-chart')
    @include('departemen.partial._chart-pie')
    @include('departemen.partial._chart-jenis')
    @include('departemen.partial._chart-efektif')
    @include('departemen.partial._chart-progres')
</div>