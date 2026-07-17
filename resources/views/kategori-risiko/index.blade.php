<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h1 class="text-lg font-bold text-slate-900">
                Manajemen Kategori Risiko
            </h1>
            <p class="text-sm text-slate-500">
                Kelola kategori untuk pemisahan data SMAP, Departemen, dan Top Risk.
            </p>
        </div>
    </x-slot>

    @php
        $activeFilter = request('filter', '');
        $searchQuery = request('search', '');
    @endphp

    <div class="space-y-6" 
         x-data="{
            filterType: '{{ $activeFilter }}',
            search: '{{ $searchQuery }}',
            filterData() {
                const rows = document.querySelectorAll('[data-category-row]');
                let visible = 0;
                rows.forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    const rowType = row.dataset.type.toLowerCase();
                    const matchSearch = this.search === '' || name.includes(this.search.toLowerCase());
                    const matchType = this.filterType === '' || rowType === this.filterType.toLowerCase();
                    
                    if (matchSearch && matchType) {
                        row.style.display = '';
                        visible++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                const countEl = document.getElementById('visible-count');
                if (countEl) countEl.textContent = visible;
            },
            
            setFilter(type) {
                this.filterType = type;
                this.search = '';
                this.filterData();
                
                const url = new URL(window.location.href);
                if (type) {
                    url.searchParams.set('filter', type);
                } else {
                    url.searchParams.delete('filter');
                }
                url.searchParams.delete('search');
                window.history.pushState({}, '', url);
            }
         }"
         x-init="filterData()">

        {{-- Stats Cards --}}
        <div class="grid gap-6 md:grid-cols-3">

            {{-- Total --}}
            <div 
                @click="setFilter('')"
                class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
                :class="filterType === '' ? 'ring-2 ring-indigo-500 ring-offset-2' : ''">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">
                            Total Kategori
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $categories->count() }}
                        </h2>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.59 13.41L11 3.83a2 2 0 00-2.83 0L3.83 8.17a2 2 0 000 2.83L13.41 20.6a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.84z"/>
                            <circle cx="7.5" cy="7.5" r="1.3" fill="currentColor"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- SMAP --}}
            <div 
                @click="setFilter('smap')"
                class="rounded-lg border border-purple-200 bg-purple-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
                :class="filterType === 'smap' ? 'ring-2 ring-purple-500 ring-offset-2' : ''">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600">
                            Kategori SMAP
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-purple-700">
                            {{ $categories->where('type','smap')->count() }}
                        </h2>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-white text-purple-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-3-3v6"/>
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Departemen --}}
            <div 
                @click="setFilter('departemen')"
                class="rounded-lg border border-blue-200 bg-blue-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg cursor-pointer"
                :class="filterType === 'departemen' ? 'ring-2 ring-blue-500 ring-offset-2' : ''">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600">
                            Kategori Departemen
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-blue-700">
                            {{ $categories->where('type','departemen')->count() }}
                        </h2>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-white text-blue-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- Toolbar: Search + Cari | Tambah --}}
        <div class="flex items-center gap-2">

            {{-- Search + Cari --}}
            <form method="GET" action="{{ route('kategori-risiko.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama kategori..."
                       class="w-64 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Cari
                </button>
            </form>

            <div class="flex-1"></div>

            {{-- Tambah --}}
            <a href="{{ route('kategori-risiko.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </a>
        </div>

        {{-- Jarak antara toolbar dan tabel --}}
        <div class="mt-6"></div>

        {{-- Table Section --}}
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="rounded-tl-lg px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Nama Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Tipe (Alokasi)
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide">
                                Keterangan
                            </th>
                            <th class="rounded-tr-lg px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 transition border-b border-slate-300"
                                data-category-row
                                data-name="{{ strtolower($category->nama_kategori) }}"
                                data-type="{{ strtolower($category->type) }}">
                                <td class="px-6 py-4 text-left">
                                    <div class="font-semibold text-slate-900">
                                        {{ $category->nama_kategori }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold 
                                        {{ $category->type === 'smap' ? 'bg-purple-50 text-purple-700' : 
                                           ($category->type === 'departemen' ? 'bg-blue-50 text-blue-700' : 
                                           'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left text-sm text-slate-600">
                                    {{ $category->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('kategori-risiko.show', $category->id_kategori) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('kategori-risiko.edit', $category->id_kategori) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('kategori-risiko.destroy', $category->id_kategori) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua data risiko yang terkait akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center border-b border-slate-300">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
                                        </svg>
                                    </div>

                                    <div class="mt-3 text-sm font-semibold text-slate-900">
                                        @if(request('search') || request('filter'))
                                            Tidak ada kategori yang sesuai dengan filter
                                        @else
                                            Belum ada data kategori risiko
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-slate-500">
                                        @if(request('search') || request('filter'))
                                            Coba ubah kata kunci atau filter yang digunakan.
                                        @else
                                            Tambahkan kategori risiko baru untuk mulai mengelompokkan data risiko.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout> 