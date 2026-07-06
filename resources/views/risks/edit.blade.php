<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Edit Risk</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Perbarui data risiko yang sudah ada.</p>
    </x-slot>

    <form method="POST" action="{{ route('risks.update', $risk->id_monitoring) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="grid gap-6 lg:grid-cols-2">

                {{-- Unit Kerja --}}
                <div>
                    <label for="id_unit" class="block text-sm font-semibold text-slate-700">
                        Unit Kerja <span class="text-rose-500">*</span>
                    </label>
                    <select id="id_unit" name="id_unit"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit', $risk->id_unit) == $unit->id_unit)>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_unit')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="id_category" class="block text-sm font-semibold text-slate-700">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select id="id_category" name="id_category"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id_category }}" @selected(old('id_category', $risk->id_category) == $cat->id_category)>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_category')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Risk Event Detail --}}
                <div class="lg:col-span-2">
                    <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">
                        Risk Event Detail <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="risk_event_deta" name="risk_event_deta" rows="3"
                              class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
                    @error('risk_event_deta')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Level --}}
                <div>
                    <label for="id_level" class="block text-sm font-semibold text-slate-700">
                        Level <span class="text-rose-500">*</span>
                    </label>
                    <select id="id_level" name="id_level"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Level</option>
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id_level }}" @selected(old('id_level', $risk->id_level) == $lvl->id_level)>
                                {{ $lvl->level_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_level')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Value --}}
                <div>
                    <label for="value" class="block text-sm font-semibold text-slate-700">
                        Value (Skor Current) <span class="text-rose-500">*</span>
                    </label>
                    <input id="value" type="number" name="value" value="{{ old('value', $risk->value) }}" min="1"
                           class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('value')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Inherent --}}
                <div>
                    <label for="inherent" class="block text-sm font-semibold text-slate-700">
                        Inherent <span class="text-rose-500">*</span>
                    </label>
                    <input id="inherent" type="number" name="inherent" value="{{ old('inherent', $risk->inherent) }}" min="1"
                           class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('inherent')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Trend --}}
                <div>
                    <label for="trend" class="block text-sm font-semibold text-slate-700">
                        Trend <span class="text-rose-500">*</span>
                    </label>
                    <select id="trend" name="trend"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Naik" @selected(old('trend', $risk->trend) === 'Naik')>↑ Naik</option>
                        <option value="Stabil" @selected(old('trend', $risk->trend) === 'Stabil')>→ Stabil</option>
                        <option value="Turun" @selected(old('trend', $risk->trend) === 'Turun')>↓ Turun</option>
                    </select>
                    @error('trend')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">
                        Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" name="type"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Proyek" @selected(old('type', $risk->type) === 'Proyek')>Proyek</option>
                        <option value="Non-Proyek" @selected(old('type', $risk->type) === 'Non-Proyek')>Non-Proyek</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" @selected(old('status', $risk->status ? '1' : '0') === '1')>Aktif</option>
                        <option value="0" @selected(old('status', $risk->status ? '1' : '0') === '0')>Non-Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('risks.index') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</x-admin-layout>
