<x-admin-layout>

    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Risk Register</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Monitoring seluruh data risiko perusahaan</p>
    </x-slot>

    <div class="space-y-6">

      <div
    x-data="{ openFilter: false }"
    class="space-y-6"
>

    <div class="flex items-center justify-between">

        @can('risk.create')
        <a href="{{ route('risks.create') }}"
            class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">

            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>

            Tambah Risk
        </a>
        @endcan

        <div class="flex gap-3">

            <button
                @click="openFilter=true"
                class="rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">

                Filter
            </button>

        </div>

    </div>

    <div
    x-show="openFilter"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    style="display:none;"
>

    <div
        @click.away="openFilter=false"
class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl overflow-y-auto max-h-[80vh]"        >

        <div class="mb-6 flex items-center justify-between">

            <h2 class="text-xl font-bold">
                Filter Risk Register
            </h2>

            <button
                type="button"
                @click="openFilter=false"
                class="text-2xl text-slate-500 hover:text-black">
                ×
            </button>

        </div>

        <form method="GET" action="{{ route('risks.index') }}">

            {{-- SEMUA FILTER LAMA DITARUH DI SINI --}}

            {{-- Search --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                <div class="lg:col-span-3">
                    <label class="mb-2 block text-sm font-semibold">
                        Cari Risk Event
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="w-full rounded-xl border"
                        placeholder="Cari risk...">
                </div>

                {{-- Unit --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Unit Kerja
                    </label>

                    <select name="unit_id" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        @foreach($units as $unit)

                            <option
                                value="{{ $unit->id_unit }}"
                                @selected($unitId == $unit->id_unit)>

                                {{ $unit->nama_unit }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Kategori --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Kategori
                    </label>

                    <select name="category_id" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        @foreach($categories as $cat)

                            <option
                                value="{{ $cat->id_category }}"
                                @selected($categoryId == $cat->id_category)>

                                {{ $cat->category_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Level --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Level
                    </label>

                    <select name="level_id" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        @foreach($levels as $lvl)

                            <option
                                value="{{ $lvl->id_level }}"
                                @selected($levelId == $lvl->id_level)>

                                {{ $lvl->level_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Type --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Type
                    </label>

                    <select name="type" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        <option value="Proyek" @selected($type=='Proyek')>
                            Proyek
                        </option>

                        <option value="Non-Proyek" @selected($type=='Non-Proyek')>
                            Non-Proyek
                        </option>

                    </select>

                </div>

                {{-- Trend --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Trend
                    </label>

                    <select name="trend" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        <option value="Naik" @selected($trend=='Naik')>Naik</option>

                        <option value="Stabil" @selected($trend=='Stabil')>Stabil</option>

                        <option value="Turun" @selected($trend=='Turun')>Turun</option>

                    </select>

                </div>

                {{-- Status --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Status
                    </label>

                    <select name="status" class="w-full rounded-xl border">

                        <option value="">Semua</option>

                        <option value="1" @selected($status==='1')>
                            Aktif
                        </option>

                        <option value="0" @selected($status==='0')>
                            Non Aktif
                        </option>

                    </select>

                </div>

            </div>

            <div class="mt-8 flex justify-end gap-3">

                <a
                    href="{{ route('risks.index') }}"
                    class="rounded-xl border px-5 py-2">
                    Reset
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-white">
                    Submit
                </button>

            </div>

        </form>

    </div>

</div>
    

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Unit Kerja</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Risk Event</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Level</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Value</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Inherent</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Trend</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($risks as $risk)
                            <tr class="hover:bg-slate-50">

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-500">
                                    {{ ($risks->currentPage() - 1) * $risks->perPage() + $loop->iteration }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-700">
                                    {{ $risk->unit?->nama_unit ?? '–' }}
                                </td>

                                <td class="max-w-xs px-4 py-4 text-sm text-slate-600">
                                    <div class="line-clamp-2">{{ $risk->risk_event_deta }}</div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                    {{ $risk->category?->category_name ?? '–' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $risk->getLevelColorClass() }}">
                                        {{ $risk->level?->level_name ?? '–' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-slate-700">
                                    {{ $risk->value }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-slate-700">
                                    {{ $risk->inherent }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="text-sm font-semibold {{ $risk->getTrendColorClass() }}">
                                        {{ $risk->getTrendIcon() }} {{ $risk->trend }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $risk->type === 'Proyek' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $risk->type }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $risk->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $risk->status ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">

                                        @can('risk.edit')
                                            <a href="{{ route('risks.edit', $risk->id_monitoring) }}"
                                               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                                </svg>
                                                Edit
                                            </a>
                                        @endcan

                                        @can('risk.delete')
                                            <form method="POST" action="{{ route('risks.destroy', $risk->id_monitoring) }}"
                                                  onsubmit="return confirm('Yakin ingin menghapus risk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-sm font-semibold text-slate-900">Belum ada data risk</div>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan risk baru atau ubah filter pencarian.</p>
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

</x-admin-layout>
