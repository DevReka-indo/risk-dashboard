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

    <div class="mb-6 rounded-lg bg-slate-200 p-2">
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('smap-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                class="rounded-lg py-3 text-center text-sm font-semibold transition
                {{ $activeTab === 'data'
                    ? 'bg-indigo-600 text-white'
                    : 'text-slate-700 hover:bg-slate-300' }}">
                Data Risk SMAP
            </a>
            <a href="{{ route('smap-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
                class="rounded-lg py-3 text-center text-sm font-semibold transition
                {{ $activeTab === 'dashboard'
                    ? 'bg-indigo-600 text-white'
                    : 'text-slate-700 hover:bg-slate-300' }}">
                Dashboard Risiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('smap._tab-chart')
    @else

    {{-- Alpine wrapper hanya untuk filter panel --}}
    <div x-data="{
        filterOpen: false,
        filterUnit: '{{ $unitId ?? '' }}',
        filterCategory: '{{ $categoryId ?? '' }}',
        filterStatus: '{{ $status ?? '' }}',
        applyFilter() {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'data');
            url.searchParams.set('unit_id', this.filterUnit);
            url.searchParams.set('category_id', this.filterCategory);
            url.searchParams.set('status', this.filterStatus);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilter() {
            this.filterUnit = '';
            this.filterCategory = '';
            this.filterStatus = '';
        }
    }">

        {{-- Toolbar: Search + Cari | Filters + Tambah --}}
        <div class="mb-4 flex items-center gap-2">

            {{-- Search + Cari --}}
            <form method="GET" action="{{ route('smap-risk.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="data">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari Peristiwa Risiko..."
                       class="w-64 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Cari
                </button>
            </form>

            <div class="flex-1"></div>

            {{-- Filters --}}
            <button type="button"
                    @click="filterOpen = true"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm0 0v7.5" />
                </svg>
                Filter
            </button>

            {{-- Tambah --}}
            <a href="{{ route('smap-risk.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </a>
        </div>

        {{-- Filter Floating Modal --}}
        <div x-show="filterOpen"
             x-transition.opacity
             class="fixed inset-0 z-50"
             style="display:none;"
             @click.self="filterOpen = false">

            {{-- Card floating di kanan atas --}}
            <div x-show="filterOpen"
                 x-transition:enter="transition transform duration-200 ease-out"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition transform duration-150 ease-in"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute bg-white"
                 style="top: 140px; right: 24px; width: 320px; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.14); padding: 24px;">

                {{-- Header --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <button type="button" @click="filterOpen = false"
                                style="background:none; border:none; cursor:pointer; padding:0; display:flex; align-items:center;">
                            <svg style="width:18px;height:18px;color:#1e293b;" fill="none" stroke="#1e293b" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                        </button>
                        <span style="font-size:15px; font-weight:700; color:#1e293b;">Property Filter</span>
                    </div>
                    <button type="button" @click="resetFilter()"
                            style="background:none; border:none; cursor:pointer; font-size:13px; font-weight:600; color:#4F7EF0;">
                        Reset all
                    </button>
                </div>

                {{-- Dropdown: Unit Kerja --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#1e293b; margin-bottom:6px;">Unit Kerja</label>
                    <div style="position:relative;">
                        <select x-model="filterUnit"
                                style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:8px; padding:9px 36px 9px 12px; font-size:13px; color:#64748b; background:#fff; cursor:pointer; outline:none;">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                        <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#94a3b8; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Kategori --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#1e293b; margin-bottom:6px;">Kategori</label>
                    <div style="position:relative;">
                        <select x-model="filterCategory"
                                style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:8px; padding:9px 36px 9px 12px; font-size:13px; color:#64748b; background:#fff; cursor:pointer; outline:none;">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Status --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#1e293b; margin-bottom:6px;">Status</label>
                    <div style="position:relative;">
                        <select x-model="filterStatus"
                                style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:8px; padding:9px 36px 9px 12px; font-size:13px; color:#64748b; background:#fff; cursor:pointer; outline:none;">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Tombol Add Filter --}}
                <div style="display:flex; justify-content:flex-end;">
                    <button type="button" @click="applyFilter()"
                            style="background:#4F7EF0; color:#fff; border:none; border-radius:10px; padding:10px 28px; font-size:14px; font-weight:700; cursor:pointer; transition:background 0.2s;"
                            onmouseover="this.style.background='#3b66d9'"
                            onmouseout="this.style.background='#4F7EF0'">
                        Add Filter
                    </button>
                </div>

            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="rounded-tl-lg px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Risiko
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Unit Kerja
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Inherent
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Target
                            </th>
                            <th class="whitespace-nowrap px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Monitoring Terakhir
                            </th>
                            <th class="rounded-tr-lg px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($smapRisks as $smapRisk)
                            @php
                                $monitoringTerakhir = $smapRisk->latestPeriode;
                                $isAktif     = $smapRisk->status === 'Aktif';
                            @endphp
                            <tr class="hover:bg-slate-50 transition border-b border-slate-300">
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                    <div class="mb-1">
                                    @if ($smapRisk->status)
                                        <span class="inline-flex rounded-lg bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                    </div>
                                        <div class="font-semibold text-slate-900">
                                            {{ $smapRisk->risk_event_deta }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Dibuat: {{ $smapRisk->created_at?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $smapRisk->kategoriRisiko->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        <span class="inline-flex rounded bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{-- Perbaikan variabel $unit menjadi $smapRisk->unitKerja --}}
                                            {{ $smapRisk->unitKerja->nama_unit ?? '-' }}
                                        </span>
                                    </div>
                                </td>
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
                                <td class="px-6 py-4">
                                    @if ($monitoringTerakhir)
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap gap-1 items-center">
                                                <span class="inline-flex rounded bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                    Nilai {{ $monitoringTerakhir->value ?? 0 }}
                                                </span>
                                                <span class="inline-flex rounded px-3 py-1 text-xs font-semibold">
                                                    {{ $monitoringTerakhir->levelRisiko->nama_level ?? '-' }}
                                                </span>
                                            </div>
                                            <div class="text-xs font-bold text-slate-900">
                                                {{ $monitoringTerakhir->quarter }} {{ $monitoringTerakhir->year }}
                                            </div>

                                            <div class="text-xs text-slate-500">
                                                <div>
                                                    Trend:
                                            </div>
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

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('smap-risk.show', $smapRisk->id_smap) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>
                                        <a href="{{ route('smap-risk.edit', $smapRisk->id_smap) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
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
                                <td colspan="8" class="px-6 py-12 text-center border-b border-slate-300">
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
