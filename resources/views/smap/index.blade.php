<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk SMAP
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring daftar risiko SMAP berdasarkan unit kerja, kategori, dan status.
        </p>
    </x-slot>

    @php
        $activeTab = request()->query('tab', 'data');
    @endphp

    <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
        <div class="grid gap-2 sm:grid-cols-2">
            <a
                href="{{ route('smap-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'data' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Data Risk SMAP
            </a>

            <a
                href="{{ route('smap-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Dashboard Risiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('smap._tab-chart')
    @else
        <div class="space-y-6">
            {{-- Filter Section --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('smap-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
                    <input type="hidden" name="tab" value="data">

                    {{-- Search --}}
                    <div class="lg:col-span-4">
                        <label for="search" class="block text-sm font-semibold text-slate-700">
                            Cari Risiko
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama peristiwa risiko..."
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="lg:col-span-3">
                        <label for="category_id" class="block text-sm font-semibold text-slate-700">
                            Kategori
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)@selected($categoryId == $category->id_kategori)
                                <option value="{{ $category->id_kategori }}" >
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Unit Kerja --}}
                    <div class="lg:col-span-3">
                        <label for="unit_id" class="block text-sm font-semibold text-slate-700">
                            Unit Kerja
                        </label>

                        <select
                            id="unit_id"
                            name="unit_id"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id_unit }}" @selected((string) $unitId === (string) $unit->id_unit)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="lg:col-span-2">
                        <label for="status" class="block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="1" @selected($status === '1')>Aktif</option>
                            <option value="0" @selected($status === '0')>Tidak Aktif</option>
                        </select>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-col gap-3 sm:flex-row lg:col-span-12 lg:justify-between">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">
                                Filter
                            </button>

                            <a
                                href="{{ route('smap-risk.index', ['tab' => 'data']) }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>
                        </div>

                        <a
                            href="{{ route('smap-risk.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Risk SMAP
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Risiko
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Kategori
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Unit Kerja
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Inherent
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Target
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Monitoring Terakhir
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse ($smapRisks as $smapRisk)
                                @php
                                    $monitoringTerakhir = $smapRisk->latestPeriode;
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    {{-- 1. Kolom Risiko --}}
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <div class="font-bold text-slate-900">
                                                {{ $smapRisk->risk_event_deta }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                Proyek
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 2. Kolom Kategori --}}
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $smapRisk->kategoriRisiko->nama_kategori ?? '-' }}
                                    </td>

                                    {{-- 3. Kolom Unit Kerja --}}
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $smapRisk->unitKerja->nama_unit ?? '-' }}
                                    </td>

                                    {{-- 4. Kolom Inherent --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <span class="inline-flex rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                                Skor: {{ $smapRisk->inherent ?? 0 }}
                                            </span>
                                            <div>
                                                <span class="text-xs text-slate-500 font-medium">
                                                    @php
                                                        $inherentVal = (int)($smapRisk->inherent ?? 0);
                                                        $levelName = 'Low';
                                                        if ($inherentVal >= 6 && $inherentVal <= 11) $levelName = 'Low Mod';
                                                        elseif ($inherentVal >= 12 && $inherentVal <= 15) $levelName = 'Moderate';
                                                        elseif ($inherentVal >= 16 && $inherentVal <= 19) $levelName = 'Mod High';
                                                        elseif ($inherentVal >= 20) $levelName = 'High';
                                                    @endphp
                                                    {{ $levelName }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 5. Kolom Target --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <span class="inline-flex rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                                Skor: {{ $smapRisk->inherent_target ?? $smapRisk->target_value ?? 0 }}
                                            </span>
                                            <div>
                                                <span class="text-xs text-slate-500 font-medium">
                                                    @php
                                                        $targetVal = (int)($smapRisk->inherent_target ?? $smapRisk->target_value ?? 0);
                                                        $targetLevelName = 'Low';
                                                        if ($targetVal >= 6 && $targetVal <= 11) $targetLevelName = 'Low Mod';
                                                        elseif ($targetVal >= 12 && $targetVal <= 15) $targetLevelName = 'Moderate';
                                                        elseif ($targetVal >= 16 && $targetVal <= 19) $targetLevelName = 'Mod High';
                                                        elseif ($targetVal >= 20) $targetLevelName = 'High';
                                                    @endphp
                                                    {{ $targetLevelName }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 6. Kolom Monitoring Terakhir --}}
                                    <td class="px-6 py-4">
                                        @if ($monitoringTerakhir)
                                            <div class="space-y-1">
                                                <div class="text-xs font-bold text-slate-900">
                                                    {{ $monitoringTerakhir->quarter }} {{ $monitoringTerakhir->year }}
                                                </div>

                                                <div class="flex flex-wrap gap-1 items-center">
                                                    <span class="inline-flex rounded bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                                        Nilai {{ $monitoringTerakhir->value ?? 0 }}
                                                    </span>

                                                    <span class="inline-flex rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                                        {{ $monitoringTerakhir->levelRisiko->nama_level ?? '-' }}
                                                    </span>
                                                </div>

                                                <div class="text-[11px] text-slate-500">
                                                    Trend:
                                                    @if(($monitoringTerakhir->trend ?? '') === 'Naik')
                                                        <span class="font-medium text-rose-600">→ Naik</span>
                                                    @elseif(($monitoringTerakhir->trend ?? '') === 'Turun')
                                                        <span class="font-medium text-emerald-600">→ Turun</span>
                                                    @else
                                                        <span class="font-medium text-slate-500">→ Stabil</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Belum ada monitoring</span>
                                        @endif
                                    </td>

                                    {{-- 7. Kolom Status --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($smapRisk->status)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 8. Kolom Aksi --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                       <div class="flex items-center justify-end gap-1">
                                            {{-- Detail --}}
                                            <a href="{{ route('smap-risk.show', $smapRisk->id_smap) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Detail
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('smap-risk.edit', $smapRisk->id_smap) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>

                                            {{-- Hapus --}}
                                            <form method="POST" action="{{ route('smap-risk.destroy', $smapRisk->id_smap) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus data Risk SMAP ini?')"
                                                class="m-0 inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                            </svg>
                                        </div>

                                        <div class="mt-3 text-sm font-semibold text-slate-900">
                                            Data Risk SMAP belum tersedia
                                        </div>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Tambahkan risiko baru untuk SMAP.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($smapRisks->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $smapRisks->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-admin-layout>
