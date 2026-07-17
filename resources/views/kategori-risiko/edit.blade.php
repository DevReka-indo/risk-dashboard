<x-admin-layout>
    <x-slot name="header">
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
                    Edit Kategori Risiko
                </h1>
                <p class="text-sm text-slate-500">
                    Edit data kategori risiko yang sudah ada.
                </p>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <div class="font-semibold">Terjadi kesalahan:</div>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-3xl border-2 border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b-2 border-slate-200 bg-slate-50 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Informasi Kategori</h2>
                    <p class="text-xs text-slate-500">Edit data kategori risiko yang sudah ada.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('kategori-risiko.update', $category->id_kategori) }}" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Kolom Kiri --}}
                <div class="space-y-6">
                    {{-- Nama Kategori --}}
                    <div>
                        <label for="nama_kategori" class="block text-sm font-semibold text-slate-700">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="nama_kategori"
                            type="text"
                            name="nama_kategori"
                            value="{{ old('nama_kategori', $category->nama_kategori) }}"
                            required
                            class="mt-2 w-full rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition placeholder:text-slate-400"
                            placeholder="Masukkan nama kategori..."
                        >
                        @error('nama_kategori')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipe Alokasi --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tipe Alokasi <span class="text-rose-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 rounded-xl border-2 border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition {{ old('type', $category->type) === 'smap' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-400' : '' }}">
                                <input
                                    type="radio"
                                    name="type"
                                    value="smap"
                                    class="h-4 w-4 border-2 border-slate-300 text-indigo-600 focus:ring-indigo-500 transition"
                                    @checked(old('type', $category->type) === 'smap')
                                >
                                <span class="text-sm font-medium text-slate-700">SMAP</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border-2 border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition {{ old('type', $category->type) === 'departemen' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-400' : '' }}">
                                <input
                                    type="radio"
                                    name="type"
                                    value="departemen"
                                    class="h-4 w-4 border-2 border-slate-300 text-indigo-600 focus:ring-indigo-500 transition"
                                    @checked(old('type', $category->type) === 'departemen')
                                >
                                <span class="text-sm font-medium text-slate-700">Departemen</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">Kategori akan digunakan untuk modul yang dipilih.</p>
                    </div>
                </div>

                {{-- Kolom Kanan: Keterangan --}}
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700">
                        Keterangan
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="6"
                        class="mt-2 w-full rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition placeholder:text-slate-400"
                        placeholder="Masukkan keterangan tambahan..."
                    >{{ old('keterangan', $category->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end border-t-2 border-slate-200 pt-6">
                <a
                    href="{{ route('kategori-risiko.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    Batalkan
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>