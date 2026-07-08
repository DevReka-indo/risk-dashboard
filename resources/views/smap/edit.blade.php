<x-admin-layout>
    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Edit Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Perbarui data risiko SMAP sistem.</p>
    </x-slot>

    <div x-data="smapRiskForm('{{ old('value', $risk->value) }}', '{{ old('inherent', $risk->inherent) }}', '{{ old('trend', $risk->trend) }}')">
        <form method="POST" action="{{ route('smap-risk.update', $risk->id_smap) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- BLOCK 1: INFORMASI UTAMA RISIKO --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
                <div>
                    <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">Nama Peristiwa Risiko <span class="text-rose-500">*</span></label>
                    <textarea id="risk_event_deta" name="risk_event_deta" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 @error('risk_event_deta') border-rose-500 @enderror">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
                    @error('risk_event_deta') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="id_kategori" class="block text-sm font-semibold text-slate-700">Kategori Risiko <span class="text-rose-500">*</span></label>
                        <select id="id_kategori" name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('id_kategori') border-rose-500 @enderror">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}" {{ old('id_kategori', $risk->id_kategori) == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nilai Otomatisasi (Input Tersembunyi/Readonly) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Value (1-25)</label>
                            <input type="number" name="value" x-model.number="value" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Inherent</label>
                            <input type="number" name="inherent" x-model.number="inherent" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- Status Aktif (Style Card) --}}
                <div x-data="{ active: {{ old('status', $risk->status) == '1' ? 'true' : 'false' }} }">
                    <input type="hidden" name="status" :value="active ? '1' : '0'">
                    <div @click="active = !active" :class="active ? 'border-indigo-200 bg-indigo-50/40' : 'border-slate-200 bg-white'" class="flex items-start gap-3 rounded-2xl border p-4 cursor-pointer transition">
                        <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border" :class="active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-300'">
                            <svg x-show="active" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M16.7 4.1a.7.7 0 01.2 1l-8 10.5a.7.7 0 01-1.1.1l-4.5-4.5a.7.7 0 011-1l3.9 3.9 7.4-9.8a.7.7 0 011-.1z"/></svg>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-slate-800">Risiko Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCK 2: UNIT KERJA (Style Grid List) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-6">Unit Kerja Terkait</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" x-data="{ selectedUnit: '{{ old('id_unit', $risk->id_unit) }}' }">
                    @foreach ($units as $unit)
                        <label :class="selectedUnit == '{{ $unit->id_unit }}' ? 'border-indigo-600 ring-2 ring-indigo-600/10 bg-indigo-50/10' : 'border-slate-100 bg-white'" class="flex items-center gap-3 rounded-2xl border p-4 cursor-pointer transition hover:bg-slate-50">
                            <input type="radio" name="id_unit" value="{{ $unit->id_unit }}" x-model="selectedUnit" class="h-4 w-4 border-slate-300 text-indigo-600">
                            <span class="text-sm font-medium text-slate-800">{{ $unit->nama_unit }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- BUTTON ACTION --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('smap-risk.index') }}" class="rounded-2xl border px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
