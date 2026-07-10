<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Unit Kerja
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Kelola master data unit kerja untuk modul Top Risk, SMAP, dan Departemen.
        </p>
    </x-slot>

    <div class="space-y-6">
        {{-- Filter Section --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 lg:grid-cols-12 lg:items-end"
                 x-data="{
                     search: '',
                     filter() {
                         const rows = document.querySelectorAll('[data-unit-row]');
                         let visible = 0;
                         rows.forEach(row => {
                             const name = row.dataset.name.toLowerCase();
                             const ket = row.dataset.keterangan.toLowerCase();
                             const match = this.search === '' || name.includes(this.search.toLowerCase()) || ket.includes(this.search.toLowerCase());
                             if (match) {
                                 row.style.display = '';
                                 visible++;
                             } else {
                                 row.style.display = 'none';
                             }
                         });
                         const countEl = document.getElementById('unit-visible-count');
                         if (countEl) countEl.textContent = visible;
                     }
                 }"
                 x-init="filter()">

                {{-- Search --}}
                <div class="lg:col-span-4">
                    <label for="search" class="block text-sm font-semibold text-slate-700">
                        Cari Unit Kerja
                    </label>

                    <input
                        id="search"
                        type="text"
                        x-model="search"
                        @input="filter()"
                        placeholder="Cari nama unit atau keterangan..."
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- Tombol Reset --}}
                <div class="lg:col-span-3 flex items-end gap-3">
                    <button
                        type="button"
                        @click="search = ''; filter()"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </button>
                </div>

                {{-- Tombol Tambah (di sisi kanan) --}}
                <div class="lg:col-span-5 flex justify-end">
                    @can('unit-kerja.create')
                        <a
                            href="{{ route('unit-kerja.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Unit Kerja
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Unit Kerja
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Keterangan
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                Risiko Terkait
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Dibuat
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse ($unitKerja as $unit)
                            <tr class="hover:bg-slate-50"
                                data-unit-row
                                data-name="{{ strtolower($unit->nama_unit) }}"
                                data-keterangan="{{ strtolower($unit->keterangan ?? '') }}">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
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

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $unit->keterangan ?: '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $unit->risiko_count ?? 0 }} risiko
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ $unit->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('unit-kerja.show', $unit) }}"
                                            class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        @can('unit-kerja.edit')
                                            <a
                                                href="{{ route('unit-kerja.edit', $unit) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('unit-kerja.delete')
                                            <form method="POST" action="{{ route('unit-kerja.destroy', $unit) }}" onsubmit="return confirm('Yakin ingin menghapus unit kerja ini?')">
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
                                <td colspan="5" class="px-6 py-12 text-center">
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