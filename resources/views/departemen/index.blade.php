<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Risk Register</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Monitoring seluruh data risiko perusahaan</p>
    </x-slot>

    @php $activeTab = request()->query('tab', 'data'); @endphp

    {{-- Tab Navigasi Data / Dashboard --}}
    <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
        <div class="grid gap-2 sm:grid-cols-2">
            <a href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
               {{ $activeTab === 'data' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Data Department
            </a>
            <a href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
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
        <div x-data="{ openFilter: false }" class="space-y-6">
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('department-risk.index') }}" class="flex flex-1 items-center gap-3 max-w-xl w-full">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari Risk Departemen"
                            class="w-full rounded-2xl border-slate-200 bg-white py-2.5 pl-4 pr-4 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">
                        Cari
                    </button>

                    @if (!empty($search))
                        <a href="{{ route('department-risk.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="flex gap-3">
                    <button @click="openFilter=true" class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">
                        Filter
                    </button>
                </div>

                @can('risk.create')
                <a href="{{ route('department-risk.create') }}"
                    class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Tambah
                </a>
                @endcan
            </div>

            {{-- Filter Modal --}}
            <div x-show="openFilter" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display:none;">
                <div @click.away="openFilter=false" class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl overflow-y-auto max-h-[80vh]">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-bold">Filter Risk Register</h2>
                        <button type="button" @click="openFilter=false" class="text-2xl text-slate-500 hover:text-black">×</button>
                    </div>

                    <form method="GET" action="{{ route('department-risk.index') }}">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-sm font-semibold">Unit Kerja</label>
                                <select name="unit_id" class="w-full rounded-xl border">
                                    <option value="">Semua</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id_unit }}" @selected(($unitId ?? '') == $unit->id_unit)>
                                            {{ $unit->nama_unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold">Kategori</label>
                                <select name="category_id" class="w-full rounded-xl border">
                                    <option value="">Semua</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id_kategori }}" @selected(($categoryId ?? '') == $cat->id_kategori)>
                                            {{ $cat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold">Level</label>
                                <select name="level_id" class="w-full rounded-xl border">
                                    <option value="">Semua</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl->id_level }}" @selected(($levelId ?? '') == $lvl->id_level)>
                                            {{ $lvl->nama_level }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('department-risk.index') }}" class="rounded-xl border px-5 py-2">Reset</a>
                            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-white">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="w-[4%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">No</th>
                                <th class="w-[18%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Unit Kerja</th>
                                <th class="w-[28%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Risk Event</th>
                                <th class="w-[14%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Kategori</th>
                                <th class="w-[8%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Level</th>
                                <th class="w-[6%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Value</th>
                                <th class="w-[6%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Inherent</th>
                                <th class="w-[6%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Trend</th>
                                <th class="w-[5%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Type</th>
                                <th class="w-[5%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Status</th>
                                <th class="w-[10%] px-4 py-4 text-center text-xs font-bold uppercase text-slate-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                        @forelse ($risks as $risk)
                            <tr class="hover:bg-slate-50">
                                {{-- Kolom Nomor --}}
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-500">
                                {{ ($risks->currentPage() - 1) * $risks->perPage() + $loop->iteration }}
                            </td>

                            {{-- Kolom Unit Kerja --}}
                            <td class="px-4 py-4 text-sm font-medium text-slate-600">
                                <div class="line-clamp-2">{{ $risk->unitKerja?->nama_unit ?? '–' }}</div>
                            </td>

                            {{-- Kolom Risk Event --}}
                            <td class="max-w-xs px-4 py-4 text-sm text-slate-600">
                                <div class="line-clamp-2">{{ $risk->risk_event_deta }}</div>
                            </td>

                            {{-- Kolom Kategori --}}
                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $risk->kategoriRisiko?->nama_kategori ?? '–' }}
                            </td>

                            {{-- Kolom Level Risiko --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $risk->getLevelColorClass() }}">
                                    {{ $risk->levelRisiko?->nama_level ?? '–' }}
                                </span>
                            </td>

                            {{-- Kolom Value --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center text-sm font-semibold text-slate-700">
                                {{ $risk->value }}
                            </td>

                            {{-- Kolom Inherent --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center text-sm font-semibold text-slate-700">
                                {{ $risk->inherent }}
                            </td>

                            {{-- Kolom Trend --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <span class="text-sm font-semibold {{ $risk->getTrendColorClass() }}">
                                    {{ $risk->getTrendIcon() }} {{ $risk->trend }}
                                </span>
                            </td>

                            {{-- Kolom Type --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $risk->type === 'Proyek' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $risk->type }}
                                </span>
                            </td>

                            {{-- Kolom Status --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $risk->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $risk->status ? 'Aktif' : 'Non-Active' }}
                                </span>
                            </td>

                            {{-- Kolom Aksi (Tombol-Tombol) --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- 1. Tombol Detail (Show) --}}
                                    @can('risk.view')
                                        <a href="{{ route('department-risk.show', $risk->id_monitoring) }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                                            Detail
                                        </a>
                                    @endcan

                                    {{-- 2. Tombol Edit --}}
                                    @can('risk.edit')
                                        <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            Edit
                                        </a>
                                    @endcan

                                    {{-- 3. Tombol Hapus --}}
                                    @can('risk.delete')
                                        <form action="{{ route('department-risk.destroy', $risk->id_monitoring) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data risiko ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Tampilan jika data kosong --}}
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center">
                                <div class="mt-3 text-sm font-semibold text-slate-900">Belum ada data risk</div>
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
    </div>
    @endif
</x-admin-layout>
