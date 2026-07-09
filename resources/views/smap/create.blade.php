<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Tambah Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Daftarkan risiko SMAP baru ke dalam sistem.</p>
    </x-slot>

    <div class="space-y-6">
        <form method="POST" action="{{ route('smap-risk.store') }}" class="space-y-6">
            @csrf

            {{-- BLOCK 1: INFORMASI UTAMA RISIKO --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">

                {{-- Nama Peristiwa Risiko (Risk Event) --}}
                <div>
                    <label for="risk_event_deta" class="block text-sm font-semibold text-slate-700">Nama Peristiwa Risiko <span class="text-rose-500">*</span></label>
                    <textarea id="risk_event_deta" name="risk_event_deta" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('risk_event_deta') border-rose-500 @enderror" placeholder="Contoh: Keterlambatan penyelesaian proyek strategis">{{ old('risk_event_deta') }}</textarea>
                    @error('risk_event_deta') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Kategori Risiko --}}
                    <div>
                        <label for="id_kategori" class="block text-sm font-semibold text-slate-700">Kategori Risiko <span class="text-rose-500">*</span></label>
                        <select id="id_kategori" name="id_kategori" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('id_kategori') border-rose-500 @enderror">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_kategori }}" {{ old('id_kategori') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('id_kategori') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal Dibuat --}}
                    <div>
                        <label for="created_at" class="block text-sm font-semibold text-slate-700">Tanggal Dibuat <span class="text-rose-500">*</span></label>
                        <input type="date" id="created_at" name="created_at" value="{{ old('created_at', date('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('created_at') border-rose-500 @enderror">
                        @error('created_at') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Status Risiko (Card Checkbox style) --}}
                <div x-data="{ active: {{ old('status', '1') == '1' ? 'true' : 'false' }} }">
                    <!-- Tambahkan name="status" di sini dan sinkronkan value -->
                    <input type="hidden" name="status" :value="active ? '1' : '0'">

                    <div
                        @click="active = !active"
                        :class="active ? 'border-indigo-200 bg-indigo-50/40' : 'border-rose-200 bg-rose-50/40'"
                        class="flex items-start gap-3 rounded-2xl border p-4 shadow-sm cursor-pointer transition select-none"
                    >
                        <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border transition"
                            :class="active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-rose-400 bg-rose-400 text-white'">
                            <svg x-show="active" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            <span x-show="!active">X</span>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-slate-800" x-text="active ? 'Risiko Aktif' : 'Risiko Tidak Aktif'"></span>
                            <span class="block text-xs text-slate-500 mt-0.5">Status saat ini menentukan visibilitas di dashboard.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCK 2: UNIT KERJA TERKAIT --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Unit Kerja Terkait</h3>
                    <p class="mt-1 text-xs text-slate-500">Pilih unit kerja utama yang berkaitan dengan risiko ini.</p>
                </div>

                {{-- Komponen Pilihan Unit Bergaya Grid List --}}
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" x-data="{ selectedUnit: '{{ old('id_unit') }}' }">
                    @foreach ($units as $unit)
                        <label
                            :class="selectedUnit == '{{ $unit->id_unit }}' ? 'border-indigo-600 ring-2 ring-indigo-600/10 bg-indigo-50/10' : 'border-slate-100 bg-white'"
                            class="relative flex items-center gap-3 rounded-2xl border p-4 shadow-sm cursor-pointer transition hover:bg-slate-50"
                        >
                            {{-- Input Radio Tersembunyi tapi Fungsional --}}
                            <input
                                type="radio"
                                name="id_unit"
                                value="{{ $unit->id_unit }}"
                                x-model="selectedUnit"
                                class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="text-sm font-medium text-slate-800">{{ $unit->nama_unit }}</span>
                        </label>
                    @endforeach
                </div>
                @error('id_unit') <span class="text-xs text-rose-500 mt-3 block">{{ $message }}</span> @enderror
            </div>

            {{-- BUTTON ACTION --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('smap-risk.index') }}" class="rounded-2xl border px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition">Simpan Top Risk</button>
            </div>
        </form>
    </div>
</x-admin-layout>
