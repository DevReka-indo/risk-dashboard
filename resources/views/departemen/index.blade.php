<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Monitoring seluruh data risiko perusahaan</p>
    </x-slot>

    @php $activeTab = request()->query('tab', 'data'); @endphp

    {{-- Tab full width --}}
    <div class="mb-6 rounded-2xl bg-slate-200/70 dark:bg-slate-800/80 p-1.5 backdrop-blur border border-slate-300/50 dark:border-slate-700/60 shadow-inner">
        <div class="grid grid-cols-2 gap-1.5">
            <a href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
               class="rounded-xl py-2.5 text-center text-sm font-semibold transition-all duration-200
               {{ $activeTab === 'data'
                   ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/25'
                   : 'text-slate-600 dark:text-slate-300 hover:bg-slate-300/60 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white' }}">
                Data Departemen
            </a>
            <a href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
               class="rounded-xl py-2.5 text-center text-sm font-semibold transition-all duration-200
               {{ $activeTab === 'dashboard'
                   ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/25'
                   : 'text-slate-600 dark:text-slate-300 hover:bg-slate-300/60 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white' }}">
                Beranda Resiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('departemen._tab-chart')
    @else

    {{-- Alpine wrapper hanya untuk filter panel --}}
    <div x-data="{
        filterOpen: false,
        filterUnit: '{{ $unitId ?? '' }}',
        filterCategory: '{{ $categoryId ?? '' }}',
        filterLevel: '{{ $levelId ?? '' }}',
        filterTrend: '{{ request('trend', '') }}',
        filterStatus: '{{ $status ?? '' }}',
        applyFilter() {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'data');
            url.searchParams.set('unit_id', this.filterUnit);
            url.searchParams.set('category_id', this.filterCategory);
            url.searchParams.set('level_id', this.filterLevel);
            url.searchParams.set('trend', this.filterTrend);
            url.searchParams.set('status', this.filterStatus);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilter() {
            this.filterUnit = '';
            this.filterCategory = '';
            this.filterLevel = '';
            this.filterTrend = '';
            this.filterStatus = '';
        }
    }">

        {{-- Toolbar: Search + Cari | Filters + Tambah --}}
        <div class="mb-4 flex flex-wrap items-center gap-2">

            {{-- Search + Cari --}}
            <form method="GET" action="{{ route('department-risk.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="data">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari Peristiwa Risiko..."
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
                Filter
            </button>

            {{-- Tambah --}}
            <a href="{{ route('department-risk.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-all active:scale-95">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </a>
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

                {{-- Dropdown: Unit Kerja --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Unit Kerja</label>
                    <div class="relative">
                        <select x-model="filterUnit"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Kategori --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kategori</label>
                    <div class="relative">
                        <select x-model="filterCategory"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Level Resiko --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Level Resiko</label>
                    <div class="relative">
                        <select x-model="filterLevel"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Level</option>
                            @foreach ($levels as $lvl)
                                <option value="{{ $lvl->id_level }}">{{ $lvl->nama_level }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Tren --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tren</label>
                    <div class="relative">
                        <select x-model="filterTrend"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Tren</option>
                            <option value="Naik">Naik</option>
                            <option value="Turun">Turun</option>
                            <option value="Stabil">Stabil</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Dropdown: Status --}}
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                    <div class="relative">
                        <select x-model="filterStatus"
                                class="w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 pr-9 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
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
                            <th class="rounded-tl-2xl px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Risiko
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Unit Kerja
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Inherent
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Target
                            </th>
                            <th class="whitespace-nowrap px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Monitoring Terakhir
                            </th>
                            <th class="rounded-tr-2xl px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($risks as $risk)
                            @php
                                $latestPeriod = $risk->periods->first();
                                $isProyek     = $risk->type === 'Proyek';
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition border-b border-slate-200 dark:border-slate-800">
                                {{-- Risiko --}}
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <div class="mb-2 flex flex-wrap items-center gap-2">
                                            <span class="inline-block rounded-lg px-3 py-0.5 text-xs font-semibold {{ $isProyek ? 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                                                {{ $risk->type ?? '-' }}
                                            </span>

                                            {{-- Aktif / Non-Aktif --}}
                                            @if ($risk->status)
                                                <span class="inline-flex rounded-lg bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Detail Risiko --}}
                                        <div class="font-semibold text-slate-900 dark:text-slate-100 leading-tight">
                                            {{ $risk->risk_event_detail ?? $risk->risk_event_deta }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Dibuat: {{ $risk->created_at?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                                </td>

                                {{-- Unit Kerja --}}
                                <td class="px-6 py-4">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        <span class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $risk->unitKerja->nama_unit ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Inheren --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="space-y-1.5 flex flex-col items-center">
                                        <span class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            Skala: {{ $risk->inherent ?? 0 }}
                                        </span>
                                        @php
                                            $inhVal = (int)($risk->inherent ?? 0);
                                            if ($inhVal >= 20) {
                                                $iName = 'High'; $iBg = 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50'; $iDot = 'bg-rose-600';
                                            } elseif ($inhVal >= 16) {
                                                $iName = 'Mod High'; $iBg = 'bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-900/50'; $iDot = 'bg-orange-500';
                                            } elseif ($inhVal >= 12) {
                                                $iName = 'Moderate'; $iBg = 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-900/50'; $iDot = 'bg-amber-500';
                                            } elseif ($inhVal >= 6) {
                                                $iName = 'Low Mod'; $iBg = 'bg-lime-100 dark:bg-lime-950/70 text-lime-800 dark:text-lime-300 border-lime-200 dark:border-lime-900/50'; $iDot = 'bg-lime-500';
                                            } else {
                                                $iName = 'Low'; $iBg = 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50'; $iDot = 'bg-emerald-600';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $iBg }}">
                                            <span class="h-2 w-2 rounded-full {{ $iDot }}"></span>
                                            {{ $iName }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Target --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="space-y-1.5 flex flex-col items-center">
                                        <span class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            Skala: {{ $risk->target_value ?? 0 }}
                                        </span>
                                        @php
                                            $tgtVal = (int)($risk->target_value ?? 0);
                                            if ($tgtVal >= 20) {
                                                $tName = 'High'; $tBg = 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50'; $tDot = 'bg-rose-600';
                                            } elseif ($tgtVal >= 16) {
                                                $tName = 'Mod High'; $tBg = 'bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-900/50'; $tDot = 'bg-orange-500';
                                            } elseif ($tgtVal >= 12) {
                                                $tName = 'Moderate'; $tBg = 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-900/50'; $tDot = 'bg-amber-500';
                                            } elseif ($tgtVal >= 6) {
                                                $tName = 'Low Mod'; $tBg = 'bg-lime-100 dark:bg-lime-950/70 text-lime-800 dark:text-lime-300 border-lime-200 dark:border-lime-900/50'; $tDot = 'bg-lime-500';
                                            } else {
                                                $tName = 'Low'; $tBg = 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50'; $tDot = 'bg-emerald-600';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $tBg }}">
                                            <span class="h-2 w-2 rounded-full {{ $tDot }}"></span>
                                            {{ $tName }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Monitoring Terakhir --}}
                                <td class="px-6 py-4">
                                    @if ($latestPeriod && isset($latestPeriod->pivot))
                                        @php
                                            $lvlName   = $latestPeriod->nama_level ?? $latestPeriod->level ?? '-';
                                            $lvlLower  = strtolower($lvlName);
                                            if (str_contains($lvlLower, 'high') && !str_contains($lvlLower, 'mod')) {
                                                $mLvlBg = 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50'; $mDot = 'bg-rose-600';
                                            } elseif (str_contains($lvlLower, 'mod high') || str_contains($lvlLower, 'moderate to high')) {
                                                $mLvlBg = 'bg-orange-100 dark:bg-orange-950/70 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-900/50'; $mDot = 'bg-orange-500';
                                            } elseif (str_contains($lvlLower, 'moderate') || str_contains($lvlLower, 'mod')) {
                                                $mLvlBg = 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-900/50'; $mDot = 'bg-amber-500';
                                            } elseif (str_contains($lvlLower, 'low mod') || str_contains($lvlLower, 'low to moderate')) {
                                                $mLvlBg = 'bg-lime-100 dark:bg-lime-950/70 text-lime-800 dark:text-lime-300 border-lime-200 dark:border-lime-900/50'; $mDot = 'bg-lime-500';
                                            } else {
                                                $mLvlBg = 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50'; $mDot = 'bg-emerald-600';
                                            }

                                            $trendVal   = $latestPeriod->pivot->trend ?? '';
                                            $trendColor = $trendVal === 'Naik' ? 'text-rose-600 dark:text-rose-400' : ($trendVal === 'Turun' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400');
                                            $trendIcon  = $trendVal === 'Naik' ? '↑' : ($trendVal === 'Turun' ? '↓' : '→');
                                        @endphp

                                        <div class="space-y-1.5">
                                            {{-- Badge Nilai & Level Risiko --}}
                                            <div class="flex items-center gap-2 flex-nowrap">
                                                <span class="inline-flex items-center whitespace-nowrap shrink-0 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-400">
                                                    Nilai {{ $latestPeriod->pivot->value ?? 0 }}
                                                </span>

                                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0 rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $mLvlBg }}">
                                                    <span class="h-2 w-2 rounded-full {{ $mDot }}"></span>
                                                    {{ $lvlName }}
                                                </span>
                                            </div>

                                            {{-- Periode Quarter & Tahun --}}
                                            <div class="text-xs font-bold text-slate-900 dark:text-slate-100">
                                                {{ $latestPeriod->pivot->quarter ?? '-' }} {{ $latestPeriod->pivot->year ?? '' }}
                                            </div>

                                            {{-- Indikator Trend --}}
                                            <div class="flex flex-col gap-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                <span>
                                                    Trend:
                                                </span>
                                                <span class="inline-flex items-center gap-0.5 font-semibold {{ $trendColor }}">
                                                    {{ $trendIcon }} {{ $trendVal ?: '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else   
                                        <span class="text-xs font-medium italic text-slate-400">Belum ada monitoring</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('department-risk.show', $risk->id_monitoring) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 shadow-xs transition-all hover:border-indigo-300 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>
                                        <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 shadow-xs transition-all hover:border-amber-300 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/50 hover:text-amber-600 dark:hover:text-amber-400">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('department-risk.destroy', $risk->id_monitoring) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                              class="m-0 inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 shadow-xs transition hover:bg-rose-50 dark:hover:bg-rose-950/50">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center border-b border-slate-200 dark:border-slate-800">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 dark:text-indigo-400 ring-1 ring-indigo-500/10 dark:ring-indigo-500/20">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-base font-bold text-slate-900 dark:text-slate-100">
                                        Data belum tersedia
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Tambahkan risiko baru atau ubah filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($risks->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-6 py-4">
                    {{ $risks->links() }}
                </div>
            @endif
        </div>

    </div>
    @endif
</x-admin-layout>
