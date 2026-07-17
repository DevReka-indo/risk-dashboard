<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Total Risiko
        </div>
        <div class="mt-3 text-3xl font-bold text-slate-900">
            {{ $summary['total_risiko'] }}
        </div>
        <p class="mt-2 text-xs text-slate-500">
            Seluruh data risiko terdaftar.
        </p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Risiko Aktif
        </div>
        <div class="mt-3 text-3xl font-bold text-emerald-600">
            {{ $summary['risiko_aktif'] }}
        </div>
        <p class="mt-2 text-xs text-slate-500">
            Risiko dengan status aktif.
        </p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Rata-rata Nilai Risiko
        </div>
        <div class="mt-3 text-3xl font-bold text-indigo-600">
            {{ $summary['rata_rata_nilai'] }}
        </div>
        <p class="mt-2 text-xs text-slate-500">
            Periode {{ $period['label'] }}.
        </p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Tren
        </div>

        @php
            $trendClass = match ($summary['tren']) {
                'Naik' => 'text-rose-600 bg-rose-50',
                'Turun' => 'text-emerald-600 bg-emerald-50',
                'Stagnan' => 'text-slate-600 bg-slate-100',
                default => 'text-slate-600 bg-slate-100',
            };
        @endphp

        <div class="mt-3 inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $trendClass }}">
            {{ $summary['tren'] }}
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Dibandingkan {{ $period['previous_label'] }}.
        </p>
    </div>
</div>
