<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Edit Risk SMAP</h1>
        <p class="text-sm text-slate-500">Perbarui data risiko dan unit kerja terkait.</p>
    </x-slot>

    <form method="POST" action="{{ route('smap-risk.update', $risk->id_smap) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- BLOCK 1: INFORMASI UTAMA --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700">Nama Peristiwa Risiko</label>
                <textarea name="risk_event_deta" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Kategori Risiko</label>
                    <select name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_kategori }}" {{ old('id_kategori', $risk->id_kategori) == $category->id_kategori ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Tanggal Dibuat</label>
                    <input type="date" name="created_at" value="{{ old('created_at', \Carbon\Carbon::parse($risk->created_at)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm">
                </div>
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4">
                <input type="checkbox" name="status" value="1" {{ old('status', $risk->status) == '1' ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-indigo-600">
                <div>
                    <span class="block text-sm font-semibold text-slate-800">Risiko Aktif</span>
                    <span class="text-xs text-slate-500">Jika tidak dicentang, risiko akan disimpan sebagai tidak aktif.</span>
                </div>
            </div>
        </div>

        {{-- BLOCK 2: UNIT KERJA --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4">Unit Kerja Terkait</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($units as $unit)
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50">
                        <input type="radio" name="id_unit" value="{{ $unit->id_unit }}" {{ old('id_unit', $risk->id_unit) == $unit->id_unit ? 'checked' : '' }} class="h-4 w-4 text-indigo-600">
                        <span class="text-sm font-medium text-slate-800">{{ $unit->nama_unit }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('smap-risk.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700">Batal</a>
            <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
</x-admin-layout>
