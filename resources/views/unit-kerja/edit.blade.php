<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Edit Unit Kerja
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Perbarui master unit kerja.
        </p>
    </x-slot>

    <form method="POST" action="{{ route('unit-kerja.update', $unitKerja) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-6">
                <div>
                    <label for="nama_unit" class="block text-sm font-semibold text-slate-700">
                        Nama Unit
                    </label>

                    <input
                        id="nama_unit"
                        type="text"
                        name="nama_unit"
                        value="{{ old('nama_unit', $unitKerja->nama_unit) }}"
                        required
                        autofocus
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('nama_unit')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700">
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="4"
                        placeholder="Keterangan unit kerja, boleh dikosongkan"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $unitKerja->keterangan) }}</textarea>

                    @error('keterangan')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a
                href="{{ route('unit-kerja.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-admin-layout>
