<x-admin-layout>
    {{-- Memuat file JavaScript terpisah menggunakan Vite --}}
    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>

    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Edit Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Perbarui data risiko SMAP sistem.</p>
    </x-slot>

    {{-- Mengirimkan data lama dari database ($risk) ke dalam parameter fungsi js --}}
    <div x-data="smapRiskForm('{{ old('value', $risk->value) }}', '{{ old('inherent', $risk->inherent) }}', '{{ old('trend', $risk->trend) }}')">
        <form method="POST" action="{{ route('smap-risk.update', $risk->id_smap) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Unit Kerja --}}
                    <div>
                        <label for="id_unit" class="block text-sm font-semibold text-slate-700">Unit Kerja <span class="text-rose-500">*</span></label>
                        <select id="id_unit" name="id_unit" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('id_unit') border-rose-500 @enderror">
                            <option value="">Pilih Unit Kerja</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id_unit }}" {{ old('id_unit', $risk->id_unit) == $unit->id_unit ? 'selected' : '' }}>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_unit') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="id_kategori" class="block text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                        <select id="id_kategori" name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('id_kategori') border-rose-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}" {{ old('id_kategori', $risk->id_kategori) == $category->id_kategori ? 'selected' : '' }}>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Risk Event --}}
                    <div class="lg:col-span-2">
                        <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">Risk Event <span class="text-rose-500">*</span></label>
                        <textarea id="risk_event_deta" name="risk_event_deta" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('risk_event_deta') border-rose-500 @enderror" placeholder="Jelaskan detail risiko SMAP...">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
                        @error('risk_event_deta') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Value --}}
                    <div>
                        <label for="value" class="block text-sm font-semibold text-slate-700">Value (Skor 1-25) <span class="text-rose-500">*</span></label>
                        <input type="number" id="value" name="value" x-model.number="value" min="1" max="25" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('value') border-rose-500 @enderror">
                        @error('value') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Level (Otomatis) --}}
                    <div>
                        <label for="id_level" class="block text-sm font-semibold text-slate-700">Level (Otomatis) <span class="text-rose-500">*</span></label>
                        <select id="id_level" name="id_level" x-model="otomatisLevel" class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm shadow-sm pointer-events-none @error('id_level') border-rose-500 @enderror">
                            <option value="">Pilih Level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id_level }}">{{ $level->nama_level }}</option>
                            @endforeach
                        </select>
                        @error('id_level') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Inherent --}}
                    <div>
                        <label for="inherent" class="block text-sm font-semibold text-slate-700">Inherent <span class="text-rose-500">*</span></label>
                        <input type="number" id="inherent" name="inherent" x-model.number="inherent" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('inherent') border-rose-500 @enderror">
                        @error('inherent') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Trend (Otomatis) --}}
                    <div>
                        <label for="trend" class="block text-sm font-semibold text-slate-700">Trend (Otomatis) <span class="text-rose-500">*</span></label>
                        <select id="trend" name="trend" x-model="otomatisTrend" class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm shadow-sm pointer-events-none @error('trend') border-rose-500 @enderror">
                            <option value="Stabil">Stabil</option>
                            <option value="Naik">Naik</option>
                            <option value="Turun">Turun</option>
                        </select>
                        @error('trend') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm @error('status') border-rose-500 @enderror">
                            <option value="1" {{ old('status', $risk->status ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status', $risk->status ? '1' : '0') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('smap-risk.index') }}" class="rounded-2xl border px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
