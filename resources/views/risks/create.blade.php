<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Tambah Risk</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Daftarkan risiko baru ke dalam sistem.</p>
    </x-slot>

    <form method="POST" action="{{ route('risks.store') }}" class="space-y-6">
        @csrf

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
                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit') == $unit->id_unit)>
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
                            <option value="{{ $cat->id_category }}" @selected(old('id_category') == $cat->id_category)>
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
                              placeholder="Jelaskan peristiwa/kejadian risiko..."
                              class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('risk_event_deta') }}</textarea>
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
                            <option value="{{ $lvl->id_level }}" @selected(old('id_level') == $lvl->id_level)>
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
                    <input id="value" type="number" name="value" value="{{ old('value') }}" min="1"
                           placeholder="Skor risiko saat ini"
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
                    <input id="inherent" type="number" name="inherent" value="{{ old('inherent') }}" min="1"
                           placeholder="Skor risiko sebelum penanganan"
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
                        <option value="">Pilih Trend</option>
                        <option value="Naik" @selected(old('trend') === 'Naik')>↑ Naik</option>
                        <option value="Stabil" @selected(old('trend') === 'Stabil')>→ Stabil</option>
                        <option value="Turun" @selected(old('trend') === 'Turun')>↓ Turun</option>
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
                        <option value="">Pilih Type</option>
                        <option value="Proyek" @selected(old('type') === 'Proyek')>Proyek</option>
                        <option value="Non-Proyek" @selected(old('type') === 'Non-Proyek')>Non-Proyek</option>
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
                        <option value="1" @selected(old('status', '1') === '1')>Aktif</option>
                        <option value="0" @selected(old('status') === '0')>Non-Aktif</option>
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
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                Submit
            </button>
        </div>

    </form>
</x-admin-layout>
