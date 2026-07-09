<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Kategori Risiko</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Informasi lengkap kategori risiko dan data terkait.</p>
    </x-slot>

    <div class="space-y-6">
        {{-- 1. NOTIFIKASI FLASH MESSAGES --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 font-semibold shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 font-semibold shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-rose-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 2. DETAIL LENGKAP KATEGORI (dengan tombol kembali & edit) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('kategori-risiko.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>
                    <a href="{{ route('kategori-risiko.edit', $category->id_kategori) }}" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700">
                        Edit Kategori
                    </a>
                    <form method="POST" action="{{ route('kategori-risiko.destroy', $category->id_kategori) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua data risiko yang terkait akan ikut terhapus.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-2xl border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-xs text-slate-400">Seluruh informasi data kategori risiko ini.</p>

            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Kategori</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $category->nama_kategori }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tipe Alokasi</span>
                    <span class="mt-1 block">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold 
                            {{ $category->type === 'smap' ? 'bg-purple-50 text-purple-700' : 
                               ($category->type === 'departemen' ? 'bg-blue-50 text-blue-700' : 
                               'bg-slate-100 text-slate-600') }}">
                            {{ ucfirst($category->type) }}
                        </span>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">ID Kategori</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $category->id_kategori }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $category->keterangan ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Dibuat</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Terakhir Diperbarui</span>
                    <span class="mt-1 block text-sm text-slate-700">{{ $category->updated_at ? $category->updated_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- 3. STATISTIK DATA RISIKO TERKAIT --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Statistik Data Risiko Terkait</h2>
            <p class="text-xs text-slate-400">Jumlah data risiko yang menggunakan kategori ini.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Risiko</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $category->risiko->count() + $category->smapMonitorings->count() + $category->depMonitorings->count() }}
                            </p>
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
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $category->risiko->count() }}</p>
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
                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $category->smapMonitorings->count() + $category->depMonitorings->count() }}
                            </p>
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
                <p class="text-xs text-slate-400">Daftar semua data risiko yang menggunakan kategori ini.</p>
            </div>

            <div class="p-6">
                @php
                    $hasData = $category->risiko->isNotEmpty() || 
                               $category->smapMonitorings->isNotEmpty() || 
                               $category->depMonitorings->isNotEmpty();
                @endphp

                @if($hasData)
                    <div class="space-y-6">
                        {{-- Top Risk --}}
                        @if($category->risiko->isNotEmpty())
                            <div>
                                <h3 class="mb-3 text-sm font-semibold text-slate-700">
                                    Top Risk ({{ $category->risiko->count() }})
                                </h3>
                                <div class="overflow-hidden border border-slate-200 rounded-2xl">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Nama Peristiwa Risiko</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($category->risiko as $index => $risk)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $risk->nama_peristiwa_risiko }}</td>
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
                        @endif

                        {{-- SMAP Monitoring --}}
                        @if($category->smapMonitorings->isNotEmpty())
                            <div>
                                <h3 class="mb-3 text-sm font-semibold text-slate-700">
                                    SMAP Risk ({{ $category->smapMonitorings->count() }})
                                </h3>
                                <div class="overflow-hidden border border-slate-200 rounded-2xl">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Risk Event</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Unit</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($category->smapMonitorings as $index => $risk)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $risk->risk_event_deta }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $risk->unitKerja->nama_unit ?? '-' }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('smap-risk.show', $risk->id_smap) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Departemen Monitoring --}}
                        @if($category->depMonitorings->isNotEmpty())
                            <div>
                                <h3 class="mb-3 text-sm font-semibold text-slate-700">
                                    Departemen Risk ({{ $category->depMonitorings->count() }})
                                </h3>
                                <div class="overflow-hidden border border-slate-200 rounded-2xl">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Risk Event</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Unit</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($category->depMonitorings as $index => $risk)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $risk->risk_event_deta }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $risk->unitKerja->nama_unit ?? '-' }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('departement-risk.show', $risk->id_monitoring) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">
                            Belum ada data risiko yang menggunakan kategori ini.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>