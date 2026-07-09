<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Tambah Risk Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Daftarkan risiko baru ke dalam sistem.</p>
    </x-slot>

    <form method="POST" action="{{ route('department-risk.store') }}" class="space-y-6"
          x-data="smapRiskForm('{{ old('value', '') }}', '{{ old('inherent', '') }}', '{{ old('trend', 'Stabil') }}')">
        @csrf
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Unit Kerja --}}
                <div>
                    <label for="id_unit" class="block text-sm font-semibold text-slate-700">Unit Kerja <span class="text-rose-500">*</span></label>
                    <select id="id_unit" name="id_unit" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit') == $unit->id_unit)>{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @error('id_unit') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="id_category" class="block text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                    <select id="id_category" name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_kategori }}" @selected(old('id_kategori') == $category->id_kategori)>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('id_kategori') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Risk Event --}}
                <div class="lg:col-span-2">
                    <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">Risk Event <span class="text-rose-500">*</span></label>
                    <textarea id="risk_event_deta" name="risk_event_deta" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan detail risiko...">{{ old('risk_event_deta') }}</textarea>
                    @error('risk_event_deta') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">Type <span class="text-rose-500">*</span></label>
                    <select id="type" name="type" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Proyek" @selected(old('type') === 'Proyek')>Proyek</option>
                        <option value="Non-Proyek" @selected(old('type') === 'Non-Proyek')>Non-Proyek</option>
                    </select>
                    @error('type') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" @selected(old('status', '1') === '1')>Aktif</option>
                        <option value="0" @selected(old('status') === '0')>Non-Aktif</option>
                    </select>
                    @error('status') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('department-risk.index') }}" class="rounded-2xl border px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg">Tambahkan Risk</button>
        </div>
    </form>

</x-admin-layout>
