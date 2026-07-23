<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h1 class="text-lg font-bold text-slate-900">
                Unit Kerja
            </h1>
            <p class="text-sm text-slate-500">
                Kelola master data unit kerja untuk modul Top Risk, SMAP, dan Departemen.
            </p>
        </div>
    </x-slot>

    {{-- Alpine wrapper untuk filter panel --}}
    <div x-data="{
        filterOpen: false,
        filterRiskCount: '{{ $riskCountFilter ?? '' }}',
        filterCreated: '{{ $createdFilter ?? '' }}',
        applyFilter() {
            const url = new URL(window.location.href);
            url.searchParams.set('risk_count', this.filterRiskCount);
            url.searchParams.set('created', this.filterCreated);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilter() {
            this.filterRiskCount = '';
            this.filterCreated = '';
        }
    }">

        {{-- Toolbar: Search + Cari | Filters + Tambah --}}
        <div class="mb-4 flex flex-wrap items-center gap-2">

            {{-- Search + Cari --}}
            <form method="GET" action="{{ route('unit-kerja.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama unit atau keterangan..."
                       class="w-64 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/90 px-3.5 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all active:scale-95">
                    Cari
                </button>
            </form>

            <div class="flex-1"></div>

            {{-- Filters --}}
            <button type="button"
                    @click="filterOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/90 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <svg class="h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm0 0v7.5" />
                </svg>
                Filters
            </button>

            {{-- Tambah --}}
            @can('unit-kerja.create')
                <a href="{{ route('unit-kerja.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-all active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Unit Kerja
                </a>
            @endcan
        </div>

        {{-- Filter Floating Modal --}}
        <div x-show="filterOpen"
             x-transition.opacity
             class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs"
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
                 class="absolute bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-2xl p-6"
                 style="top: 140px; right: 24px; width: 320px;">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="filterOpen = false" class="text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                        </button>
                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">Property Filter</span>
                    </div>
                    <button type="button" @click="resetFilter()" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Reset all
                    </button>
                </div>

                {{-- Dropdown: Total Risiko --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Total Risiko</label>
                    <div class="relative">
                        <select x-model="filterRiskCount"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Unit</option>
                            <option value="has_risk">Memiliki Risiko (>0)</option>
                            <option value="no_risk">Belum Ada Risiko (=0)</option>
                            <option value="high_risk">Banyak Risiko (>=5)</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Tanggal Dibuat --}}
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Waktu Dibuat</label>
                    <div class="relative">
                        <select x-model="filterCreated"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Waktu</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="this_year">Tahun Ini</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Tombol Add Filter --}}
                <div class="flex justify-end">
                    <button type="button" @click="applyFilter()"
                            class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700 active:scale-95">
                        Add Filter
                    </button>
                </div>

            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl shadow-slate-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            {{-- PERBAIKAN: Alignment disesuaikan ke text-left agar sejajar dengan isi data --}}
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Unit Kerja
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Keterangan
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Risiko Terkait
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Dibuat
                            </th>
                            <th class="rounded-tr-lg px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unitKerja as $unit)
                            <tr class="hover:bg-slate-50 transition border-b border-slate-300"
                                data-unit-row
                                data-name="{{ strtolower($unit->nama_unit) }}"
                                data-keterangan="{{ strtolower($unit->keterangan ?? '') }}">
                                
                                {{-- Unit Kerja --}}
                                <td class="whitespace-nowrap px-6 py-4 text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-indigo-50 text-indigo-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m4.5-6h1.5m-1.5 3h1.5m-1.5 3h1.5" />
                                            </svg>
                                        </div>

                                        <div>
                                            <div class="font-semibold text-slate-900">
                                                {{ $unit->nama_unit }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                ID: {{ $unit->id_unit }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-6 py-4 text-left text-sm text-slate-600">
                                    {{ $unit->keterangan ?: '-' }}
                                </td>

                                {{-- Risiko Terkait --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                        {{ $unit->risiko_count ?? 0 }} risiko
                                    </span>
                                </td>

                                {{-- Dibuat --}}
                                <td class="whitespace-nowrap px-6 py-4 text-left text-xs text-slate-600">
                                    {{ $unit->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Detail --}}
                                        <a href="{{ route('unit-kerja.show', $unit) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>

                                        @can('unit-kerja.edit')
                                        {{-- Edit --}}
                                        <a href="{{ route('unit-kerja.edit', $unit) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        @endcan

                                        @can('unit-kerja.delete')
                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('unit-kerja.destroy', $unit) }}" 
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')"
                                            class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-100 bg-white px-3 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition-all duration-200 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center border-b border-slate-300">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18" />
                                        </svg>
                                    </div>

                                    <div class="mt-3 text-sm font-semibold text-slate-900">
                                        @if(request('search'))
                                            Tidak ada unit kerja yang sesuai dengan pencarian
                                        @else
                                            Unit kerja belum tersedia
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-slate-500">
                                        @if(request('search'))
                                            Coba ubah kata kunci pencarian.
                                        @else
                                            Tambahkan unit kerja untuk mulai mengelola master data.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($unitKerja->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $unitKerja->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>