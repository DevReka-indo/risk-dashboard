<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Top Risk Register
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring daftar risiko perusahaan berdasarkan kategori, unit kerja, dan status.
        </p>
    </x-slot>

    @php
        $activeTab = request()->query('tab', 'data');
    @endphp

    <div class="mb-6 rounded-lg bg-slate-200 p-2">
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('top-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                class="rounded-lg py-3 text-center text-sm font-semibold transition
                {{ $activeTab === 'data'
                    ? 'bg-indigo-600 text-white'
                    : 'text-slate-700 hover:bg-slate-300' }}">
                Data Top Risk
            </a>
            <a href="{{ route('top-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
                class="rounded-lg py-3 text-center text-sm font-semibold transition
                {{ $activeTab === 'dashboard'
                    ? 'bg-indigo-600 text-white'
                    : 'text-slate-700 hover:bg-slate-300' }}">
                Dashboard Risiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('top-risk._tab-chart')
    @else

    {{-- Alpine wrapper untuk filter panel --}}
    <div x-data="{
        filterOpen: false,
        filterKategori: '{{ $kategoriId ?? '' }}',
        filterUnit: '{{ $unitId ?? '' }}',
        filterStatus: '{{ $statusAktif ?? '' }}',
        applyFilter() {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'data');
            url.searchParams.set('id_kategori', this.filterKategori);
            url.searchParams.set('id_unit', this.filterUnit);
            url.searchParams.set('status', this.filterStatus);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilter() {
            this.filterKategori = '';
            this.filterUnit = '';
            this.filterStatus = '';
        }
    }">

        {{-- Toolbar: Search + Cari | Filters + Tambah --}}
        <div class="mb-4 flex items-center gap-2"> 

            {{-- Search + Cari --}}
            <form method="GET" action="{{ route('top-risk.index') }}" class="flex items-center gap-2">
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
            <a href="{{ route('top-risk.create') }}"
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

                {{-- Dropdown: Kategori --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#1e293b; margin-bottom:6px;">Kategori</label>
                    <div style="position:relative;">
                        <select x-model="filterKategori"
                                style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:8px; padding:9px 36px 9px 12px; font-size:13px; color:#64748b; background:#fff; cursor:pointer; outline:none;">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriRisiko as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#94a3b8; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Unit Kerja --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#1e293b; margin-bottom:6px;">Unit Kerja</label>
                    <div style="position:relative;">
                        <select x-model="filterUnit"
                                style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:8px; padding:9px 36px 9px 12px; font-size:13px; color:#64748b; background:#fff; cursor:pointer; outline:none;">
                            <option value="">Semua Unit</option>
                            @foreach ($unitKerja as $unit)
                                <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
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
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
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
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Unit Kerja
                            </th>
                            <th class="whitespace-nowrap px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Monitoring Terakhir
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Status
                            </th>
                            <th class="rounded-tr-lg px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topRisks as $topRisk)
                            @php
                                $monitoringTerakhir = $topRisk->monitoringBulanan->first();
                                $levelUrutan = (int) ($monitoringTerakhir?->level?->urutan ?? 0);
                                $levelBadgeClass = match ($levelUrutan) {
                                    1 => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                                    2 => 'bg-lime-100 text-lime-800 ring-1 ring-lime-200',
                                    3 => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
                                    4 => 'bg-orange-100 text-orange-800 ring-1 ring-orange-200',
                                    5 => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                                    default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
                                };
                            @endphp

                            <tr class="hover:bg-slate-50 transition border-b border-slate-300">
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <div class="font-semibold text-slate-900">
                                            {{ $topRisk->nama_peristiwa_risiko }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Dibuat: {{ $topRisk->tanggal_dibuat?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ $topRisk->kategori->nama_kategori ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        @forelse ($topRisk->unitKerja as $unit)
                                            <span class="inline-flex rounded bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                {{ $unit->nama_unit }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-slate-400">-</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($monitoringTerakhir)
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap gap-2">
                                                <span class="inline-flex rounded bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                    Nilai {{ $monitoringTerakhir->nilai }}
                                                </span>

                                                <span class="inline-flex rounded px-3 py-1 text-xs font-semibold {{ $levelBadgeClass }}">
                                                    {{ $monitoringTerakhir->level->nama_level ?? '-' }}
                                                </span>
                                            </div>
                                            <div class="text-sm font-semibold text-slate-900">
                                                {{ str_pad((string) $monitoringTerakhir->bulan, 2, '0', STR_PAD_LEFT) }}/{{ $monitoringTerakhir->tahun }}
                                            </div>



                                            <div class="text-xs text-slate-500">
                                                <div> Efektivitas:
                                                </div>
                                                
                                                <span class="font-semibold">
                                                    {{ $monitoringTerakhir->aturanEfektivitas->hasil ?? 'Belum ada pembanding' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">Belum ada monitoring</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($topRisk->is_aktif)
                                        <span class="inline-flex rounded bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Detail --}}
                                        <a href="{{ route('top-risk.show', $topRisk) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('top-risk.edit', $topRisk) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>

                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('top-risk.destroy', $topRisk) }}" 
                                              onsubmit="return confirm('Yakin ingin menghapus data Top Risk ini?')"
                                              class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-100 bg-white px-3 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition-all duration-200 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center border-b border-slate-300">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                        </svg>
                                    </div>

                                    <div class="mt-3 text-sm font-semibold text-slate-900">
                                        Data Top Risk belum tersedia
                                    </div>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan risiko baru untuk mulai melakukan monitoring.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($topRisks->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $topRisks->links() }}
                </div>
            @endif
        </div>

    </div>
    @endif
</x-admin-layout>