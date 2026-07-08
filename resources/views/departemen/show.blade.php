<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Risk Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Detail risiko dan informasi lengkap data departemen.</p>
    </x-slot>

    <div class="space-y-6">

        {{-- Card Utama: Info Risiko --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div>
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $risk->kategoriRisiko?->nama_kategori ?? '-' }}
                        </span>

                        @if ($risk->status)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Non-Aktif
                            </span>
                        @endif

                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $risk->type ?? '-' }}
                        </span>
                    </div>

                    <h2 class="max-w-4xl text-xl font-bold leading-8 text-slate-900">
                        {{ $risk->risk_event_deta }}
                    </h2>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $risk->unitKerja?->nama_unit ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('department-risk.index') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>

                    @can('risk.edit')
                        <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                            Edit Risiko
                        </a>
                    @endcan
                </div>

            </div>
        </div>

        {{-- Card Parameter Risiko --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">Parameter Risiko</h2>
                <p class="mt-1 text-sm text-slate-500">Nilai, level, inherent, dan trend risiko saat ini.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-sm font-medium text-slate-500">Value</div>
                    <div class="mt-3 text-3xl font-bold text-indigo-600">{{ $risk->value }}</div>
                    <p class="mt-2 text-xs text-slate-500">Skor risiko saat ini.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-sm font-medium text-slate-500">Inherent</div>
                    <div class="mt-3 text-3xl font-bold text-slate-900">{{ $risk->inherent }}</div>
                    <p class="mt-2 text-xs text-slate-500">Skor risiko sebelum penanganan.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-sm font-medium text-slate-500">Level Risiko</div>
                    <div class="mt-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $risk->getLevelColorClass() }}">
                            {{ $risk->levelRisiko?->nama_level ?? '-' }}
                        </span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Berdasarkan nilai saat ini.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-sm font-medium text-slate-500">Trend</div>

                    @php
                        $trendClass = match ($risk->trend) {
                            'Naik' => 'text-rose-600 bg-rose-50',
                            'Turun' => 'text-emerald-600 bg-emerald-50',
                            'Stabil' => 'text-slate-600 bg-slate-100',
                            default => 'text-slate-600 bg-slate-100',
                        };
                        $trendIcon = match ($risk->trend) {
                            'Naik' => '↑',
                            'Turun' => '↓',
                            'Stabil' => '→',
                            default => '–',
                        };
                    @endphp

                    <div class="mt-3 inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $trendClass }}">
                        {{ $trendIcon }} {{ $risk->trend }}
                    </div>

                    <p class="mt-3 text-xs text-slate-500">Arah pergerakan nilai risiko.</p>
                </div>

            </div>
        </div>

        {{-- Card Detail Lengkap --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <p class="mt-1 text-sm text-slate-500">Seluruh informasi data risiko ini.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Unit Kerja</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $risk->unitKerja?->nama_unit ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kategori</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $risk->kategoriRisiko?->nama_kategori ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Type</div>
                    <div class="mt-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                            {{ $risk->type === 'Proyek' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $risk->type ?? '-' }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status</div>
                    <div class="mt-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                            {{ $risk->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $risk->status ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dibuat</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $risk->created_at?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terakhir Diubah</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $risk->updated_at?->format('d M Y, H:i') ?? '-' }}
                    </div>
                </div>

                <div class="sm:col-span-2 xl:col-span-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Risk Event Detail</div>
                    <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                        {{ $risk->risk_event_deta }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-admin-layout>
