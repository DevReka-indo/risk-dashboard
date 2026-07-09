<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Edit Risk Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Perbarui data risiko yang sudah ada.</p>
    </x-slot>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-600">
            <p class="font-bold mb-2">Terjadi kesalahan pengisian data:</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('department-risk.update', $risk->id_monitoring) }}" class="space-y-6"
          x-data="smapRiskForm('{{ old('value', $risk->value) }}', '{{ old('inherent', $risk->inherent) }}', '{{ old('trend', $risk->trend) }}')">
        @csrf
        @method('PUT')

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Unit Kerja --}}
                <div>
                    <label for="id_unit" class="block text-sm font-semibold text-slate-700">Unit Kerja <span class="text-rose-500">*</span></label>
                    <select id="id_unit" name="id_unit" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit', $risk->id_unit) == $unit->id_unit)>{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="id_category" class="block text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                    <select id="id_category" name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_kategori }}" @selected(old('id_kategori', $risk->id_kategori) == $category->id_kategori)>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Risk Event --}}
                <div class="lg:col-span-2">
                    <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">Risk Event <span class="text-rose-500">*</span></label>
                    <textarea id="risk_event_deta" name="risk_event_deta" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">Type <span class="text-rose-500">*</span></label>
                    <select id="type" name="type" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Proyek" @selected(old('type', $risk->type) === 'Proyek')>Proyek</option>
                        <option value="Non-Proyek" @selected(old('type', $risk->type) === 'Non-Proyek')>Non-Proyek</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" @selected(old('status', $risk->status) == 1)>Aktif</option>
                        <option value="0" @selected(old('status', $risk->status) == 0)>Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('department-risk.index') }}" class="rounded-2xl border px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</x-admin-layout>
