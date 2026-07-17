<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('kategori-risiko.index') }}"
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
                        Detail Kategori Risiko
                    </h1>
                    <p class="text-l text-slate-500">
                        Informasi lengkap kategori risiko dan data terkait
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
                        Seluruh informasi data kategori risiko ini.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 lg:flex-shrink-0">
                    <a href="{{ route('kategori-risiko.edit', $category->id_kategori) }}"
                        class="rounded-lg border-2 border-indigo-500 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 transition">
                        Edit
                    </a>
                    <form method="POST"
                        action="{{ route('kategori-risiko.destroy', $category->id_kategori) }}"
                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
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
                    {{-- TIPE ALOKASI --}}
                    <div class="rounded-lg border-2 border-slate-200 bg-white p-4">
                        <h4 class="text-l font-semibold text-slate-600">
                            Tipe Alokasi
                        </h4>
                        <div class="mt-3 flex h-10 items-center justify-center rounded-lg bg-indigo-100 text-sm font-bold text-indigo-600">
                            {{ strtoupper($category->type) }}
                        </div>
                    </div>

                    {{-- NAMA --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Nama Kategori
                        </h4>
                        <div class="mt-4 text-xl font-semibold text-slate-900">
                            {{ $category->nama_kategori }}
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Keterangan
                        </h4>
                        <div class="mt-6 text-2xl font-bold text-slate-900">
                            {{ $category->keterangan ?? '-' }}
                        </div>
                    </div>

                    {{-- ID --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            ID Kategori
                        </h4>
                        <div class="mt-3 flex h-10 items-center justify-center rounded-xl bg-emerald-100 text-xl font-bold text-emerald-600">
                            {{ $category->id_kategori }}
                        </div>
                    </div>

                    {{-- CREATED --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Tanggal Dibuat
                        </h4>
                        <div class="mt-3 text-xl font-bold text-emerald-600">
                            {{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>

                    {{-- UPDATED --}}
                    <div class="rounded-2xl border-2 border-slate-200 bg-white p-5">
                        <h4 class="text-xl font-semibold text-slate-600">
                            Terakhir Diperbarui
                        </h4>
                        <div class="mt-3 text-xl font-bold text-blue-600">
                            {{ $category->updated_at ? $category->updated_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kalkulasi Risiko Terkait --}}
        <div class="rounded-3xl border-2 border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Kalkulasi Risiko Terkait</h2>
            <p class="text-xs text-slate-400">Jumlah data risiko yang menggunakan kategori ini.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Risiko --}}
                <a href="{{ route('kategori-risiko.show', $category->id_kategori) }}?filter=all"
                    class="rounded-2xl border-2 p-4 transition cursor-pointer block
                    {{ request('filter', 'all') === 'all' 
                        ? 'border-indigo-500 bg-indigo-50' 
                        : 'border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300' }}">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Risiko</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $category->risiko->count() + $category->smapMonitorings->count() + $category->depMonitorings->count() }}
                    </p>
                </a>

                {{-- Top Risk --}}
                <a href="{{ route('kategori-risiko.show', $category->id_kategori) }}?filter=top"
                    class="rounded-2xl border-2 p-4 transition cursor-pointer block
                    {{ request('filter') === 'top' 
                        ? 'border-indigo-500 bg-indigo-50' 
                        : 'border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300' }}">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Top Risk</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $category->risiko->count() }}</p>
                </a>

                {{-- SMAP --}}
                <a href="{{ route('kategori-risiko.show', $category->id_kategori) }}?filter=smap"
                    class="rounded-2xl border-2 p-4 transition cursor-pointer block
                    {{ request('filter') === 'smap' 
                        ? 'border-indigo-500 bg-indigo-50' 
                        : 'border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300' }}">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">SMAP</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $category->smapMonitorings->count() }}</p>
                </a>

                {{-- Departemen --}}
                <a href="{{ route('kategori-risiko.show', $category->id_kategori) }}?filter=departemen"
                    class="rounded-2xl border-2 p-4 transition cursor-pointer block
                    {{ request('filter') === 'departemen' 
                        ? 'border-indigo-500 bg-indigo-50' 
                        : 'border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300' }}">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Departemen</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $category->depMonitorings->count() }}</p>
                </a>
            </div>
        </div>

        {{-- Data Risiko Terkait --}}
        <div class="rounded-3xl border-2 border-slate-200 bg-white shadow-sm">
            <div class="border-b-2 border-slate-200 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Data Risiko Terkait</h2>
                <p class="text-xs text-slate-400">Daftar semua data risiko yang menggunakan kategori ini.</p>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-x-auto p-6">
                @php
                    $filterType = request('filter', 'all');
                    
                    // Kumpulkan data berdasarkan filter
                    $allRisks = collect();
                    
                    if ($filterType === 'all' || $filterType === 'top') {
                        foreach ($category->risiko as $risk) {
                            $allRisks->push([
                                'type' => 'Top Risk',
                                'id' => $risk->id_risiko,
                                'name' => $risk->nama_peristiwa_risiko,
                                'created_at' => $risk->created_at,
                                'kategori' => $risk->kategori->nama_kategori ?? '-',
                                'unit' => $risk->unitKerja->pluck('nama_unit')->implode(', '),
                                'status' => $risk->is_aktif,
                                'route' => route('top-risk.show', $risk->id_risiko),
                                'type_badge' => 'bg-indigo-100 text-indigo-700'
                            ]);
                        }
                    }
                    
                    if ($filterType === 'all' || $filterType === 'smap') {
                        foreach ($category->smapMonitorings as $risk) {
                            $allRisks->push([
                                'type' => 'SMAP',
                                'id' => $risk->id_smap,
                                'name' => $risk->risk_event_deta,
                                'created_at' => $risk->created_at,
                                'kategori' => $risk->kategoriRisiko->nama_kategori ?? '-',
                                'unit' => $risk->unitKerja->nama_unit ?? '-',
                                'status' => $risk->status,
                                'route' => route('smap-risk.show', $risk->id_smap),
                                'type_badge' => 'bg-purple-100 text-purple-700'
                            ]);
                        }
                    }
                    
                    if ($filterType === 'all' || $filterType === 'departemen') {
                        foreach ($category->depMonitorings as $risk) {
                            $allRisks->push([
                                'type' => 'Departemen',
                                'id' => $risk->id_monitoring,
                                'name' => $risk->risk_event_deta,
                                'created_at' => $risk->created_at,
                                'kategori' => $risk->kategoriRisiko->nama_kategori ?? '-',
                                'unit' => $risk->unitKerja->nama_unit ?? '-',
                                'status' => $risk->status,
                                'route' => route('department-risk.show', $risk->id_monitoring),
                                'type_badge' => 'bg-blue-100 text-blue-700'
                            ]);
                        }
                    }
                    
                    $allRisks = $allRisks->sortByDesc('created_at');
                    $hasData = $allRisks->isNotEmpty();
                @endphp

                @if($hasData)
                    <div class="overflow-hidden border-2 border-slate-200 rounded-2xl">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="bg-indigo-600 text-white">
                                    <th class="rounded-tl-[28px] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Risiko</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Kategori</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Unit Kerja</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Tipe</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide border-r border-slate-300">Status</th>
                                    <th class="rounded-tr-[28px] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($allRisks as $risk)
                                    <tr class="hover:bg-slate-50 border-b border-slate-200">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-slate-900">{{ $risk['name'] }}</div>
                                            <div class="text-xs text-slate-500">Dibuat: {{ $risk['created_at']?->format('d M Y') ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600 border-r border-slate-200">{{ $risk['kategori'] }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600 border-r border-slate-200">{{ $risk['unit'] }}</td>
                                        <td class="px-4 py-3 border-r border-slate-200">
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $risk['type_badge'] }}">
                                                {{ $risk['type'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 border-r border-slate-200">
                                            @if($risk['status'])
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ $risk['route'] }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
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
                            @if(request('filter') && request('filter') !== 'all')
                                Tidak ada data risiko dengan tipe "{{ ucfirst(request('filter')) }}" pada kategori ini.
                            @else
                                Belum ada data risiko yang menggunakan kategori ini.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>