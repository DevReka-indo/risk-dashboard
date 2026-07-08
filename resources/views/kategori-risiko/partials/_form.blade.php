<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if(isset($method))
            @method($method)
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label for="nama_kategori" class="block text-sm font-semibold text-slate-700">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>

                <input
                    id="nama_kategori"
                    type="text"
                    name="nama_kategori"
                    value="{{ old('nama_kategori', $category->nama_kategori ?? '') }}"
                    required
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Masukkan nama kategori...">

                @error('nama_kategori')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-semibold text-slate-700">
                    Tipe Alokasi <span class="text-rose-500">*</span>
                </label>

                <select
                    id="type"
                    name="type"
                    required
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih Tipe</option>
                    <option value="smap" @selected(old('type', $category->type ?? '') === 'smap')>SMAP</option>
                    <option value="departemen" @selected(old('type', $category->type ?? '') === 'departemen')>Departemen</option>
                </select>

                @error('type')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-400">Kategori akan digunakan untuk modul yang dipilih.</p>
            </div>
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-semibold text-slate-700">
                Keterangan
            </label>

            <textarea
                id="keterangan"
                name="keterangan"
                rows="3"
                class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Masukkan keterangan tambahan...">{{ old('keterangan', $category->keterangan ?? '') }}</textarea>

            @error('keterangan')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                {{ $buttonText ?? 'Simpan Kategori' }}
            </button>

            <a
                href="{{ route('kategori-risiko.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>
        </div>
    </form>
</div>