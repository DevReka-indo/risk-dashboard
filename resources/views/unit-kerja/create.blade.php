<x-admin-layout>
    <x-slot name="header">
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
                    Tambah Unit Kerja
                </h1>
                <p class="text-sm text-slate-500">
                    Tambahkan master unit kerja baru.
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

    <form method="POST" action="{{ route('unit-kerja.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-3xl border-2 border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b-2 border-slate-200 bg-slate-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m4.5-6h1.5m-1.5 3h1.5m-1.5 3h1.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Informasi Unit Kerja</h2>
                        <p class="text-xs text-slate-500">Masukkan data unit kerja baru.</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Nama Unit --}}
                    <div>
                        <label for="nama_unit" class="block text-sm font-semibold text-slate-700">
                            Nama Unit <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="nama_unit"
                            type="text"
                            name="nama_unit"
                            value="{{ old('nama_unit') }}"
                            required
                            autofocus
                            placeholder="Contoh: Pemasaran"
                            class="mt-2 w-full rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition placeholder:text-slate-400"
                        >
                        @error('nama_unit')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-slate-700">
                            Keterangan
                        </label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            rows="4"
                            placeholder="Keterangan unit kerja, boleh dikosongkan"
                            class="mt-2 w-full rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition placeholder:text-slate-400"
                        >{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end border-t-2 border-slate-200 pt-6 mt-6">
                    <a
                        href="{{ route('unit-kerja.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Simpan Unit Kerja
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>