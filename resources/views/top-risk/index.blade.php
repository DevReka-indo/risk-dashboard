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

    <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
        <div class="grid gap-2 sm:grid-cols-2">
            <a
                href="{{ route('top-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'data' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Data Top Risk
            </a>

            <a
                href="{{ route('top-risk.index', array_merge(request()->except('page'), ['tab' => 'dashboard'])) }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                {{ $activeTab === 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Dashboard Risiko
            </a>
        </div>
    </div>

    @if ($activeTab === 'dashboard')
        @include('top-risk._tab-chart')
    @else
        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('top-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
                    <input type="hidden" name="tab" value="data">

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

                    <div class="lg:col-span-3">
                        <label for="id_kategori" class="block text-sm font-semibold text-slate-700">
                            Kategori
                        </label>

                        <select
                            id="id_kategori"
                            name="id_kategori"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriRisiko as $kategori)
                                <option value="{{ $kategori->id_kategori }}" @selected((int) $kategoriId === (int) $kategori->id_kategori)>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <label for="id_unit" class="block text-sm font-semibold text-slate-700">
                            Unit Kerja
                        </label>

                        <select
                            id="id_unit"
                            name="id_unit"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Unit</option>
                            @foreach ($unitKerja as $unit)
                                <option value="{{ $unit->id_unit }}" @selected((int) $unitId === (int) $unit->id_unit)>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="aktif" @selected($statusAktif === 'aktif')>Aktif</option>
                            <option value="tidak_aktif" @selected($statusAktif === 'tidak_aktif')>Tidak Aktif</option>
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
                                href="{{ route('top-risk.index', ['tab' => 'data']) }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>
                        </div>

                        <a
                            href="{{ route('top-risk.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Top Risk
                        </a>
                    </div>
                </form>
            </div>

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
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            @forelse ($topRisks as $topRisk)
                                @php
                                    $monitoringTerakhir = $topRisk->monitoringBulanan->first();
                                @endphp

                                <tr class="hover:bg-slate-50">
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
                                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
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
                                                <div class="text-sm font-semibold text-slate-900">
                                                    {{ str_pad((string) $monitoringTerakhir->bulan, 2, '0', STR_PAD_LEFT) }}/{{ $monitoringTerakhir->tahun }}
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                        Nilai {{ $monitoringTerakhir->nilai }}
                                                    </span>

                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                        {{ $monitoringTerakhir->level->nama_level ?? '-' }}
                                                    </span>
                                                </div>

                                                <div class="text-xs text-slate-500">
                                                    Efektivitas:
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
                                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('top-risk.show', $topRisk) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('top-risk.edit', $topRisk) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('top-risk.destroy', $topRisk) }}" onsubmit="return confirm('Yakin ingin menghapus data Top Risk ini?')">
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
                                    <td colspan="6" class="px-6 py-12 text-center">
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
