<div class="flex flex-row gap-6 mt-6 w-full items-stretch justify-between">

    <div class="flex-1 rounded-lg border border-slate-100 bg-white p-8 shadow-sm flex flex-col justify-between min-h-[200px]">
        <div>
            <h3 class="text-base font-semibold text-slate-500">Total Risiko SMAP</h3>
            <p class="mt-5 text-5xl font-bold text-slate-900 tracking-tight">
                {{ $summary['total_risiko'] ?? 0 }}
            </p>
        </div>
        <p class="mt-5 text-xs text-slate-400">
            Seluruh data riwayat risiko terdaftar.
        </p>
    </div>

    <div class="flex-1 rounded-lg border border-slate-100 bg-white p-8 shadow-sm flex flex-col justify-between min-h-[200px]">
        <div>
            <h3 class="text-base font-semibold text-slate-500">Risiko Aktif SMAP</h3>
            <p class="mt-5 text-5xl font-bold text-emerald-600 tracking-tight">
                {{ $summary['risiko_aktif'] ?? 0 }}
            </p>
        </div>
        <p class="mt-5 text-xs text-slate-400">
            Risiko dengan status aktif.
        </p>
    </div>

    <div class="flex-1 rounded-lg border border-slate-100 bg-white p-8 shadow-sm flex flex-col justify-between min-h-[200px]">
        <div>
            <h3 class="text-base font-semibold text-slate-500">Jumlah Departemen</h3>
            <p class="mt-5 text-5xl font-bold text-indigo-600 tracking-tight">
                {{ $summary['jumlah_departemen'] ?? 0 }}
            </p>
        </div>
        <p class="mt-5 text-xs text-slate-400">
            Periode {{ $periodText ?? '' }}.
        </p>
    </div>

</div>
