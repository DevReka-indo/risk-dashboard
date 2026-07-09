<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Department
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring daftar risiko departemen berdasarkan unit kerja, kategori, dan status.
        </p>
    </x-slot>

    @php
        $activeTab = request()->query('tab', 'data');
    @endphp

    <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
        <div class="grid gap-2 sm:grid-cols-2">
            <a
                href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'data' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Data Department
            </a>

            <a
                href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Dashboard Risiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('departemen._tab-chart')
    @else
        <div class="space-y-6">
            {{-- Filter Section --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('department-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
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
                            value="{{ $search ?? '' }}"
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
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}" @selected((string) ($categoryId ?? '') === (string) $category->id_kategori)>
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
                                <option value="{{ $unit->id_unit }}" @selected((string) ($unitId ?? '') === (string) $unit->id_unit)>
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
                            <option value="1" @selected(($status ?? '') === '1')>Aktif</option>
                            <option value="0" @selected(($status ?? '') === '0')>Tidak Aktif</option>
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
                                href="{{ route('department-risk.index', ['tab' => 'data']) }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>
                        </div>

                        @can('risk.create')
                        <a
                            href="{{ route('department-risk.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Risk Department
                        </a>
                        @endcan
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
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
                                    Monitoring Terakhir
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse ($risks as $risk)
                                @php
                                    $latestPeriod = $risk->periods->first();
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    {{-- Kolom Risiko --}}
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <div class="font-semibold text-slate-900">
                                                {{ $risk->risk_event_deta }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                Dibuat: {{ $risk->created_at?->format('d M Y') ?? '-' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Kategori --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                                    </td>

                                    {{-- Kolom Unit Kerja --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $risk->unitKerja->nama_unit ?? '-' }}
                                    </td>

                                    {{-- Kolom Monitoring Terakhir --}}
                                    <td class="px-6 py-4">
                                        @if ($latestPeriod)
                                            <div class="space-y-1">
                                                <div class="text-sm font-semibold text-slate-900">
                                                    {{ $latestPeriod->pivot->quarter . ' ' . $latestPeriod->pivot->year }}
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                        Nilai {{ $latestPeriod->pivot->value ?? 0 }}
                                                    </span>

                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                        {{ $latestPeriod->level ?? $latestPeriod->nama_level ?? '-' }}
                                                    </span>
                                                </div>

                                                <div class="text-xs text-slate-500">
                                                    Trend:
                                                    <span class="font-semibold">
                                                        @if(($latestPeriod->pivot->trend ?? '') === 'Naik')
                                                            <span class="text-rose-600">↑ Naik</span>
                                                        @elseif(($latestPeriod->pivot->trend ?? '') === 'Turun')
                                                            <span class="text-emerald-600">↓ Turun</span>
                                                        @else
                                                            <span class="text-slate-500">→ Stabil</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-400">Belum ada monitoring</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($risk->status)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @can('risk.view')
                                            <a
                                                href="{{ route('department-risk.show', $risk->id_monitoring) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Detail
                                            </a>
                                            @endcan

                                            @can('risk.edit')
                                            <a
                                                href="{{ route('department-risk.edit', $risk->id_monitoring) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Edit
                                            </a>
                                            @endcan

                                            @can('risk.delete')
                                            <form method="POST" action="{{ route('department-risk.destroy', $risk->id_monitoring) }}" onsubmit="return confirm('Yakin ingin menghapus data Risk Department ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    Hapus
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                            </svg>
                                        </div>

                                        <div class="mt-3 text-sm font-semibold text-slate-900">
                                            Data Risk Department belum tersedia
                                        </div>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Tambahkan risiko baru untuk Department.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($risks->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $risks->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-admin-layout>