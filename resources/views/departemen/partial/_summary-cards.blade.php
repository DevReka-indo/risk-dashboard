<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    {{-- Kartu Total Risiko Dept --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-slate-500">Total Risiko Dept</div>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total_risiko'] ?? 0 }}</div>
        <p class="mt-2 text-xs text-slate-500">Seluruh data risiko terdaftar.</p>
    </div>

    {{-- Kartu Risiko Aktif Dept --}}
    <div class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-slate-500">Risiko Aktif Dept</div>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-bold text-emerald-600">{{ $summary['risiko_aktif'] ?? 0 }}</div>
        <p class="mt-2 text-xs text-slate-500">Risiko dengan status aktif.</p>
    </div>

    {{-- Kartu Jumlah Departemen --}}
    <div class="relative overflow-hidden rounded-3xl border border-indigo-100 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-slate-500">Jumlah Departemen</div>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-bold text-indigo-600">{{ $units->count() }}</div>
        <p class="mt-2 text-xs text-slate-500">Periode {{ $period['label'] ?? 'Saat Ini' }}.</p>
    </div>
</div>
