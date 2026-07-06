@props([
    'items' => collect(),
    'title' => 'Nilai Top Risk',
    'subtitle' => null,
])

@php
    $items = collect($items);
    $maxNilai = max((int) $items->max('nilai'), 1);
@endphp

<div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">
                {{ $title }}
            </h2>

            @if ($subtitle)
                <p class="text-sm text-slate-500">
                    {{ $subtitle }}
                </p>
            @else
                <p class="text-sm text-slate-500">
                    Visualisasi nilai risiko berdasarkan data monitoring terbaru.
                </p>
            @endif
        </div>

        <div class="text-xs font-medium text-slate-400">
            Maks. Nilai: {{ $maxNilai }}
        </div>
    </div>

    @if ($items->isNotEmpty())
        <div class="space-y-4">
            @foreach ($items as $item)
                @php
                    $nilai = (int) ($item['nilai'] ?? 0);
                    $percentage = $maxNilai > 0 ? ($nilai / $maxNilai) * 100 : 0;
                    $levelName = $item['level'] ?? '-';
                    $levelColor = $item['kode_warna'] ?? '#64748B';
                @endphp

                <div class="grid gap-2 lg:grid-cols-[320px_1fr_64px] lg:items-center">
                    <div>
                        <div class="line-clamp-2 text-sm font-semibold text-slate-800">
                            {{ $item['nama_peristiwa_risiko'] ?? '-' }}
                        </div>

                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                            <span
                                class="inline-block h-2.5 w-2.5 rounded-full"
                                style="background-color: {{ $levelColor }};">
                            </span>
                            {{ $levelName }}
                        </div>
                    </div>

                    <div class="relative h-9 overflow-hidden rounded-2xl bg-slate-100">
                        <div
                            class="flex h-full items-center justify-end rounded-2xl px-3 text-xs font-bold text-white shadow-sm"
                            style="width: {{ $percentage }}%; min-width: {{ $nilai > 0 ? '44px' : '0px' }}; background-color: {{ $levelColor }};">
                            {{ $nilai }}
                        </div>
                    </div>

                    <div class="text-right text-sm font-bold text-slate-900">
                        {{ $nilai }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 border-t border-slate-100 pt-4">
            <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: #00B050;"></span>
                    Low
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: #92D050;"></span>
                    Low to Moderate
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: #FFC000;"></span>
                    Moderate
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: #ED7D31;"></span>
                    Moderate to High
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: #FF0000;"></span>
                    High
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl bg-slate-50 px-5 py-10 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-white text-slate-400 shadow-sm">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 2 5-7" />
                </svg>
            </div>

            <div class="mt-3 text-sm font-semibold text-slate-900">
                Belum ada data nilai Top Risk
            </div>

            <p class="mt-1 text-sm text-slate-500">
                Data akan muncul setelah monitoring bulanan diinput.
            </p>
        </div>
    @endif
</div>
