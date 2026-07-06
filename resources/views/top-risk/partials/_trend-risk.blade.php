<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">
                Trend Nilai Risiko
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Pola pergerakan nilai risiko sampai periode {{ $period['label'] }}.
            </p>
        </div>

        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-rose-50 px-3 py-1 text-rose-700">Naik</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Turun</span>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700">Fluktuatif</span>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">Stagnan</span>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            @php
                $trendStyle = match ($item['trend']) {
                    'Naik' => [
                        'card' => 'border-rose-200 bg-rose-50/50',
                        'badge' => 'bg-rose-600 text-white',
                        'icon' => 'text-rose-600 bg-rose-100',
                    ],
                    'Turun' => [
                        'card' => 'border-emerald-200 bg-emerald-50/50',
                        'badge' => 'bg-emerald-600 text-white',
                        'icon' => 'text-emerald-600 bg-emerald-100',
                    ],
                    'Fluktuatif' => [
                        'card' => 'border-amber-200 bg-amber-50/60',
                        'badge' => 'bg-amber-500 text-white',
                        'icon' => 'text-amber-600 bg-amber-100',
                    ],
                    'Stagnan' => [
                        'card' => 'border-slate-200 bg-slate-50',
                        'badge' => 'bg-slate-700 text-white',
                        'icon' => 'text-slate-600 bg-slate-200',
                    ],
                    default => [
                        'card' => 'border-slate-200 bg-white',
                        'badge' => 'bg-slate-500 text-white',
                        'icon' => 'text-slate-500 bg-slate-100',
                    ],
                };

                $trendIconPath = match ($item['trend']) {
                    'Naik'
                        => 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.63-1.095m0 0-3.75-.625m3.75.625-1.875 3.375',
                    'Turun'
                        => 'M2.25 6 9 12.75l4.306-4.306a11.95 11.95 0 0 1 5.814 5.518l2.63 1.095m0 0-3.75.625m3.75-.625-1.875-3.375',
                    'Fluktuatif' => 'M3 7.5h3.75L9 15l3-10.5 3 12 2.25-9H21',
                    'Stagnan' => 'M4.5 12h15',
                    default => 'M12 9v3.75m0 3.75h.008v.008H12V16.5',
                };

                $trendValues = collect($item['trend_values'] ?? []);
                $maxValue = max((int) $trendValues->max('value'), 1);
            @endphp

            <div class="rounded-3xl border p-5 {{ $trendStyle['card'] }}">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $trendStyle['icon'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $trendIconPath }}" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                                        R{{ $item['number'] }}
                                    </span>

                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $trendStyle['badge'] }}">
                                        {{ $item['trend'] }}
                                    </span>

                                    <span
                                        class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                        Nilai {{ $item['current_value'] }}
                                    </span>

                                    <span
                                        class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">
                                        {{ $item['level'] }}
                                    </span>
                                </div>

                                <h3 class="mt-3 text-sm font-bold leading-6 text-slate-900">
                                    {{ $item['risk_name'] }}
                                </h3>

                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $item['trend_description'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full shrink-0 xl:w-[420px]">
                        <div class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-sm">
                            <div class="mb-5 flex items-center justify-between gap-4">
                                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Riwayat Nilai
                                </div>

                                <div
                                    class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                    s.d {{ $period['label'] }}
                                </div>
                            </div>

                            @if ($trendValues->isNotEmpty())
                                <div class="overflow-x-auto pb-1">
                                    <div class="flex min-w-max items-end gap-4">
                                        @foreach ($trendValues as $trendValue)
                                            @php
                                                $height = max(((int) $trendValue['value'] / $maxValue) * 100, 10);
                                            @endphp

                                            <div class="flex w-20 shrink-0 flex-col items-center gap-2">
                                                <div class="flex h-24 w-full items-end rounded-2xl bg-slate-100 p-1.5">
                                                    <div class="w-full rounded-xl bg-indigo-500 shadow-sm"
                                                        style="height: {{ $height }}%">
                                                    </div>
                                                </div>

                                                <div
                                                    class="text-center text-[11px] font-semibold leading-4 text-slate-500">
                                                    {{ $trendValue['label'] }}
                                                </div>

                                                <div
                                                    class="rounded-full bg-slate-900 px-2.5 py-0.5 text-[11px] font-bold text-white">
                                                    {{ $trendValue['value'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div
                                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                                    <p class="text-xs font-semibold text-slate-500">
                                        Belum ada riwayat nilai.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="text-sm font-semibold text-slate-700">
                    Belum ada data monitoring pada periode ini.
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Input monitoring bulanan terlebih dahulu untuk menampilkan trend nilai risiko.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-5 rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h3 class="text-sm font-bold text-slate-900">
            Logika Trend
        </h3>

        <div class="mt-3 grid gap-3 text-sm text-slate-600 md:grid-cols-2">
            <div>
                <span class="font-bold text-slate-900">Naik:</span>
                nilai pernah naik dan tidak pernah turun sampai periode terpilih.
            </div>

            <div>
                <span class="font-bold text-slate-900">Turun:</span>
                nilai pernah turun dan tidak pernah naik sampai periode terpilih.
            </div>

            <div>
                <span class="font-bold text-slate-900">Fluktuatif:</span>
                nilai pernah naik dan juga pernah turun antar bulan.
            </div>

            <div>
                <span class="font-bold text-slate-900">Stagnan:</span>
                seluruh nilai sama atau tidak berubah antar bulan.
            </div>
        </div>
    </div>
</div>
