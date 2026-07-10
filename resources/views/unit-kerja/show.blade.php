<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Unit Kerja</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Informasi lengkap unit kerja dan data risiko terkait.</p>
    </x-slot>

    <div class="space-y-6">

        {{-- 2. DETAIL LENGKAP UNIT KERJA --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('unit-kerja.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>
                    <a href="{{ route('unit-kerja.edit', $unitKerja) }}" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700">
                        Edit Unit Kerja
                    </a>
                    <form method="POST" action="{{ route('unit-kerja.destroy', $unitKerja) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-2xl border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-xs text-slate-400">Seluruh informasi data unit kerja ini.</p>

            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Unit Kerja</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $unitKerja->nama_unit }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">ID Unit</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $unitKerja->id_unit }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $unitKerja->keterangan ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Risiko Terkait</span>
                    <span class="mt-1 block">
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                            {{ $unitKerja->risiko_count ?? 0 }} risiko
                        </span>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Dibuat</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $unitKerja->created_at ? $unitKerja->created_at->format('d M Y H:i') : '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Terakhir Diperbarui</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $unitKerja->updated_at ? $unitKerja->updated_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- 3. STATISTIK RISIKO TERKAIT --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Statistik Risiko Terkait</h2>
            <p class="text-xs text-slate-400">Jumlah data risiko yang menggunakan unit kerja ini.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Risiko</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $unitKerja->risiko_count ?? 0 }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Top Risk</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $unitKerja->risiko->count() ?? 0 }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">SMAP / Departemen</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">0</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h15.75M10.5 16.5 7.5 13.5M7.5 10.5 10.5 7.5M16.5 16.5 19.5 13.5M16.5 10.5 13.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. TABEL DATA RISIKO TERKAIT --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Data Risiko Terkait</h2>
                <p class="text-xs text-slate-400">Daftar semua data risiko yang menggunakan unit kerja ini.</p>
            </div>

            <div class="p-6">
                @if($unitKerja->risiko->isNotEmpty())
                    <div>
                        <h3 class="mb-3 text-sm font-semibold text-slate-700">
                            Top Risk ({{ $unitKerja->risiko->count() }})
                        </h3>
                        <div class="overflow-hidden border border-slate-200 rounded-2xl">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Nama Peristiwa Risiko</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($unitKerja->risiko as $index => $risk)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $risk->nama_peristiwa_risiko }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $risk->kategori->nama_kategori ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                @if($risk->is_aktif)
                                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('top-risk.show', $risk->id_risiko) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">
                            Belum ada data risiko yang menggunakan unit kerja ini.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>