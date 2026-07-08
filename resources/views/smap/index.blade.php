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
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('smap-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
                    <input type="hidden" name="tab" value="data">

                    <div class="lg:col-span-3">
                        <label for="search" class="block text-sm font-semibold text-slate-700">
                            Cari Risiko
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari risk event..."
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

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
                                <option value="{{ $category->id_kategori }}" @selected((string) $categoryId === (string) $category->id_kategori)>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-3">
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

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                    NO
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    UNIT KERJA
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    RISK EVENT
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    KATEGORI
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                    PERIODE LATEST
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                    STATUS
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                    ACTION
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse ($smapRisks as $index => $risk)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 text-center text-sm text-slate-600">
                                        {{ $smapRisks->firstItem() + $index }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $risk->unitKerja->nama_unit ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <div class="font-semibold text-slate-900">
                                                {{ $risk->risk_event_deta }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium text-slate-800">
                                        {{ $risk->latestPeriode->period->period_name ?? 'N/A' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-center">
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

                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a
                                                href="{{ route('smap-risk.show', $risk->id_smap) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('smap-risk.edit', $risk->id_smap) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('smap-risk.destroy', $risk->id_smap) }}" onsubmit="return confirm('Yakin ingin menghapus data Risk SMAP ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
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
