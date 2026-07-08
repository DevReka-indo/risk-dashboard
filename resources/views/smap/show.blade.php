<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Detail Risk SMAP
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Detail risiko SMAP berdasarkan unit kerja, kategori, dan level.
        </p>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                        </span>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $risk->getLevelColorClass() }}">
                            {{ $risk->levelRisiko->nama_level ?? '-' }}
                        </span>

                        @if ($risk->status)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <h2 class="max-w-4xl text-xl font-bold leading-8 text-slate-900">
                        {{ $risk->risk_event_deta }}
                    </h2>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-xs text-slate-500">Unit Kerja</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $risk->unitKerja->nama_unit ?? '-' }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-xs text-slate-500">Value</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $risk->value }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-xs text-slate-500">Inherent</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $risk->inherent }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-xs text-slate-500">Trend</div>
                            <div class="mt-1 font-bold text-slate-900 {{ $risk->getTrendColorClass() }}">
                                {{ $risk->getTrendIcon() }} {{ $risk->trend }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('smap-risk.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>

                    <a
                        href="{{ route('smap-risk.edit', $risk->id_smap) }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                        Edit Risiko
                    </a>

                    <form method="POST" action="{{ route('smap-risk.destroy', $risk->id_smap) }}" onsubmit="return confirm('Yakin ingin menghapus data Risk SMAP ini?')">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informasi Lengkap Risiko -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Informasi Lengkap Risiko
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Detail lengkap data risiko SMAP.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Risk Event Deta
                        </div>
                        <div class="mt-1 text-sm font-medium text-slate-900">
                            {{ $risk->risk_event_deta }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Unit Kerja
                        </div>
                        <div class="mt-1 text-sm font-medium text-slate-900">
                            {{ $risk->unitKerja->nama_unit ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Kategori Risiko
                        </div>
                        <div class="mt-1 text-sm font-medium text-slate-900">
                            {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Level Risiko
                        </div>
                        <div class="mt-1">
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $risk->getLevelColorClass() }}">
                                {{ $risk->levelRisiko->nama_level ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Value
                        </div>
                        <div class="mt-1 text-sm font-medium text-slate-900">
                            {{ $risk->value }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Inherent
                        </div>
                        <div class="mt-1 text-sm font-medium text-slate-900">
                            {{ $risk->inherent }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Trend
                        </div>
                        <div class="mt-1 text-sm font-medium {{ $risk->getTrendColorClass() }}">
                            {{ $risk->getTrendIcon() }} {{ $risk->trend }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </div>
                        <div class="mt-1">
                            @if ($risk->status)
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Metrics -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Risk Level
                        </p>
                        <p class="mt-2 text-sm font-bold text-slate-900">
                            {{ $risk->levelRisiko->nama_level ?? '-' }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $risk->getLevelColorClass() }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Risk Score
                        </p>
                        <p class="mt-2 text-sm font-bold text-slate-900">
                            {{ $risk->value }} / {{ $risk->inherent }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Trend
                        </p>
                        <p class="mt-2 text-sm font-bold {{ $risk->getTrendColorClass() }}">
                            {{ $risk->getTrendIcon() }} {{ $risk->trend }}
                        </p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $risk->getTrendColorClass() }} bg-opacity-10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h15.75M10.5 16.5 7.5 13.5M7.5 10.5 10.5 7.5M16.5 16.5 19.5 13.5M16.5 10.5 13.5 7.5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>