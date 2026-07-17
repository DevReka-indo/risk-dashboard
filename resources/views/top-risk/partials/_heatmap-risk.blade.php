<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">
                Heatmap Risiko
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Pemetaan risiko berdasarkan nilai risiko pada periode monitoring terpilih.
            </p>
        </div>

        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-800">Low</span>
            <span class="rounded-full bg-lime-100 px-3 py-1 text-lime-800">Low to Moderate</span>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">Moderate</span>
            <span class="rounded-full bg-orange-100 px-3 py-1 text-orange-800">Moderate to High</span>
            <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-800">High</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="min-w-[760px]">
            <div class="grid grid-cols-[90px_repeat(5,minmax(0,1fr))] gap-2">
                <div></div>

                <div class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Hampir Pasti Terjadi
                </div>
                <div class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Sangat Mungkin Terjadi
                </div>
                <div class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Bisa Terjadi
                </div>
                <div class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Jarang Terjadi
                </div>
                <div class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    Sangat Jarang Terjadi
                </div>

                @foreach ($heatmap['rows'] as $rowIndex => $row)
                    <div class="flex items-center justify-center rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                        Baris {{ $rowIndex + 1 }}
                    </div>

                    @foreach ($row as $cell)
                        <div class="min-h-28 rounded-2xl p-3 ring-1 {{ $cell['class'] }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-xs font-semibold opacity-75">
                                    Nilai
                                </div>
                                <div class="text-lg font-black">
                                    {{ $cell['value'] }}
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @forelse ($cell['risks'] as $risk)
                                    <span
                                        title="{{ $risk['risk_name'] }}"
                                        class="inline-flex rounded-full bg-white/70 px-2 py-1 text-[11px] font-bold shadow-sm ring-1 ring-black/5">
                                        {{ $risk['code'] }}
                                    </span>
                                @empty
                                    <span class="text-xs font-medium opacity-50">
                                        -
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h3 class="text-sm font-bold text-slate-900">
            Keterangan Risiko
        </h3>

        <div class="mt-3 grid gap-3 lg:grid-cols-2">
            @forelse ($heatmap['risks'] as $risk)
                <div class="rounded-lg border border-slate-200 bg-white p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-bold text-slate-900">
                                {{ $risk['code'] }}
                            </div>
                            <p class="mt-1 text-sm leading-5 text-slate-600">
                                {{ $risk['risk_name'] }}
                            </p>
                        </div>

                        <div class="shrink-0 text-right">
                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                Nilai {{ $risk['value'] }}
                            </span>

                            <div class="mt-2 text-xs font-semibold text-slate-500">
                                {{ $risk['level'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center lg:col-span-2">
                    <p class="text-sm font-semibold text-slate-600">
                        Belum ada data monitoring untuk ditampilkan pada heatmap.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
