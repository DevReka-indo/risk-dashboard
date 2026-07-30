@php
    // Pastikan summary memiliki semua key yang diperlukan
    $summary = $summary ?? [];
    $totalRisiko = $summary['total_risiko'] ?? 0;
    $risikoAktif = $summary['risiko_aktif'] ?? 0;
    $rataRataNilai = $summary['rata_rata_nilai'] ?? 0;
    $tren = $summary['tren'] ?? 'Stagnan';
    
    $trendClass = match ($tren) {
        'Naik' => 'text-rose-600 bg-rose-50',
        'Turun' => 'text-emerald-600 bg-emerald-50',
        'Fluktuatif' => 'text-amber-600 bg-amber-50',
        'Stagnan' => 'text-slate-600 bg-slate-100',
        default => 'text-slate-600 bg-slate-100',
    };
@endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Total Risiko
        </div>
        <div class="mt-3 text-3xl font-bold text-slate-900">
            {{ number_format($totalRisiko) }}
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
            {{ number_format($risikoAktif) }}
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
            {{ number_format($rataRataNilai, 1) }}
        </div>
        <p class="mt-2 text-xs text-slate-500">
            Periode {{ $period['label'] ?? 'terpilih' }}.
        </p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-medium text-slate-500">
            Tren
        </div>

        <div class="mt-3 inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $trendClass }}">
            {{ $tren }}
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Dibandingkan {{ $period['previous_label'] ?? 'periode sebelumnya' }}.
        </p>
    </div>
</div>