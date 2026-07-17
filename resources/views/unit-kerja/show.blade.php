<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('unit-kerja.index') }}"
                    class="flex h-11 w-11 items-center justify-center rounded-xl transition hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-slate-900"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">
                        Detail Unit Kerja
                    </h1>
                    <p class="text-l text-slate-500">
                        Informasi lengkap unit kerja dan data risiko terkait.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 font-semibold shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 font-semibold shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-rose-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ========================= DETAIL LENGKAP ========================= --}}
        <div class="rounded-3xl border-2 border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col gap-4 border-b-2 border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-slate-900">
                        Detail Lengkap
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Seluruh informasi data unit kerja ini.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 lg:flex-shrink-0">
                    <a href="{{ route('unit-kerja.edit', $unitKerja) }}"
                        class="rounded-lg border-2 border-indigo-500 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 transition">
                        Edit Unit Kerja
                    </a>
                    <form method="POST"
                        action="{{ route('unit-kerja.destroy', $unitKerja) }}"
                        onsubmit="return confirm('Yakin ingin menghapus unit kerja ini?')"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg border-2 border-red-500 bg-white px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <div class="px-3 py-3">
                <div class="grid gap-2 lg:grid-cols-3">
                    {{-- ID UNIT --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            ID Unit
                        </h4>
                        <div class="mt-3 flex h-10 items-center justify-center rounded-xl bg-emerald-100 text-xl font-bold text-emerald-600">
                            {{ $unitKerja->id_unit }}
                        </div>
                    </div>

                    {{-- NAMA UNIT KERJA --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Nama Unit Kerja
                        </h4>
                        <div class="mt-4 text-xl font-semibold text-slate-900">
                            {{ $unitKerja->nama_unit }}
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Keterangan
                        </h4>
                        <div class="mt-6 text-2xl font-bold text-slate-900">
                            {{ $unitKerja->keterangan ?? '-' }}
                        </div>
                    </div>

                    {{-- TOTAL RISIKO --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Total Risiko Terkait
                        </h4>
                        <div class="mt-3 flex h-10 items-center justify-center rounded-xl bg-indigo-100 text-xl font-bold text-indigo-600">
                            {{ $unitKerja->risiko_count ?? 0 }} risiko
                        </div>
                    </div>

                    {{-- CREATED --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Tanggal Dibuat
                        </h4>
                        <div class="mt-3 text-xl font-bold text-emerald-600">
                            {{ $unitKerja->created_at ? $unitKerja->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>

                    {{-- UPDATED --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Terakhir Diperbarui
                        </h4>
                        <div class="mt-3 text-xl font-bold text-blue-600">
                            {{ $unitKerja->updated_at ? $unitKerja->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Risiko Terkait --}}
        <div class="rounded-3xl border-2 border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Statistik Risiko Terkait</h2>
            <p class="text-xs text-slate-400">Jumlah data risiko yang menggunakan unit kerja ini.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Total Risiko --}}
                <div class="rounded-2xl border-2 border-slate-200 bg-slate-50 p-4">
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

                {{-- Top Risk --}}
                <div class="rounded-2xl border-2 border-slate-200 bg-slate-50 p-4">
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

                {{-- SMAP / Departemen --}}
                <div class="rounded-2xl border-2 border-slate-200 bg-slate-50 p-4">
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

        {{-- Data Risiko Terkait --}}
        <div class="rounded-3xl border-2 border-slate-200 bg-white shadow-sm">
            <div class="border-b-2 border-slate-200 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Data Risiko Terkait</h2>
                <p class="text-xs text-slate-400">Daftar semua data risiko yang menggunakan unit kerja ini.</p>
            </div>

            <div class="overflow-x-auto p-6">
                @if($unitKerja->risiko->isNotEmpty())
                    <div class="overflow-hidden border-2 border-slate-200 rounded-2xl">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="bg-indigo-600 text-white">
                                    <th class="rounded-tl-[28px] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">No</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Nama Peristiwa Risiko</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Kategori</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Status</th>
                                    <th class="rounded-tr-[28px] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($unitKerja->risiko as $index => $risk)
                                    <tr class="hover:bg-slate-50 border-b border-slate-200">
                                        <td class="px-4 py-3 text-sm text-slate-600 border-r border-slate-200">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-slate-900 border-r border-slate-200">{{ $risk->nama_peristiwa_risiko }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600 border-r border-slate-200">{{ $risk->kategori->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 border-r border-slate-200">
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
                @else
                    <div class="py-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
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