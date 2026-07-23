<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('department-risk.index') }}"
               class="flex h-7 w-7 items-center justify-center rounded text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Edit Risk Departemen</h1>
                <p class="text-xs text-slate-500">Perbarui data risiko yang sudah ada</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('department-risk.update', $risk->id_monitoring) }}" x-data="smapRiskForm">
        @csrf
        @method('PUT')

        {{-- TAMBAHKAN BLOK ERROR INI --}}
        @if($errors->any())
            <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b; margin-bottom: 20px;">
                <p style="font-weight: 700; margin-bottom: 5px;">Gagal menyimpan! Periksa kesalahan berikut:</p>
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- AKHIR BLOK ERROR --}}

        {{-- CARD UTAMA --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- BARIS ATAS --}}
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    {{-- KOLOM KIRI --}}
                    <td style="width:270px; padding:24px; border-right:1px solid #f1f5f9; vertical-align:top; background:#fafbfc;">
                        <div style="display:flex; flex-direction:column; gap:18px;">

                            {{-- Unit Kerja --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Unit Kerja <span style="color:#ef4444;">*</span></label>
                                <div style="position:relative;">
                                    <select name="id_unit"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; cursor:pointer; transition:border-color 0.2s;">
                                        <option value="" disabled>Pilih Unit Kerja</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit', $risk->id_unit) == $unit->id_unit)>{{ $unit->nama_unit }}</option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                @error('id_unit') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Kategori <span style="color:#ef4444;">*</span></label>
                                <div style="position:relative;">
                                    <select name="id_kategori"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; cursor:pointer; transition:border-color 0.2s;">
                                        <option value="" disabled>Pilih Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id_kategori }}" @selected(old('id_kategori', $risk->id_kategori) == $cat->id_kategori)>{{ $cat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                @error('id_kategori') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tanggal Dibuat --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Tanggal Dibuat <span style="color:#ef4444;">*</span></label>
                                <input type="date" name="created_at"
                                       value="{{ old('created_at', $risk->created_at?->format('Y-m-d') ?? date('Y-m-d')) }}"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;">
                            </div>

                            {{-- Tipe --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Tipe <span style="color:#ef4444;">*</span></label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; background:#fff;">
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="type" value="Proyek"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('type', $risk->type) === 'Proyek')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Proyek</span>
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="type" value="Non-Proyek"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('type', $risk->type) === 'Non-Proyek')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Non-Proyek</span>
                                        </label>
                                    </div>
                                </div>
                                @error('type') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Status <span style="color:#ef4444;">*</span></label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; background:#fff;">
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="status" value="1"
                                                   style="width:16p x; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('status', $risk->status ? '1' : '0') === '1')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Aktif</span>
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="status" value="0"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('status', $risk->status ? '1' : '0') === '0')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Non-Aktif</span>
                                        </label>
                                    </div>
                                </div>
                                @error('status') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </td>

                    {{-- KOLOM KANAN: Peristiwa Resiko --}}
                    <td style="padding:24px; vertical-align:top;">
                        <div style="display:flex; flex-direction:column; height:100%;">
                            <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Peristiwa Resiko <span style="color:#ef4444;">*</span></label>
                            <textarea name="risk_event_deta"
                                      placeholder="Masukkan peristiwa resiko secara detail..."
                                      style="flex:1; width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px; font-size:13px; color:#475569; background:#fff; resize:none; outline:none; min-height:240px; font-family:inherit; transition:border-color 0.2s; line-height:1.6;">{{ old('risk_event_deta', $risk->risk_event_deta) }}</textarea>
                            <div style="margin-top:6px; font-size:11px; color:#94a3b8; text-align:right;">
                                <span id="charCount">{{ strlen(old('risk_event_deta', $risk->risk_event_deta)) }}</span> karakter
                            </div>
                            @error('risk_event_deta') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                        </div>
                    </td>
                </tr>
            </table>

            {{-- DIVIDER --}}
            <div style="border-top:2px solid #f1f5f9;"></div>

            {{-- BARIS BAWAH: Nilai & Level --}}
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:24px;">
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px;">

                            {{-- Nilai Inheren --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Nilai Inheren <span style="color:#ef4444;">*</span></label>

                                <input type="number"
                                    name="inherent"
                                    x-model="inherent"
                                    x-init="inherent = '{{ old('inherent', $risk->inherent) }}'"
                                    min="1" max="25"
                                    placeholder="1 - 25"
                                    value="{{ old('inherent', $risk->inherent) }}"
                                    style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">

                                {{-- MENGGUNAKAN inherentLevel --}}
                                <input type="hidden"
                                    name="id_level"
                                    :value="inherentLevel || '{{ old('id_level', $risk->id_level) }}'">
                            </div>

                            {{-- Level Inheren (auto) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-900 dark:text-slate-100 mb-2">Level Inheren</label>
                                <div class="flex h-[38px] items-center justify-center rounded-xl border px-3 text-xs font-bold transition-all"
                                     :class="{
                                         'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-400 border-rose-300 dark:border-rose-900': (inherentLevelName === 'High' || inherentLevelName === 'Tinggi'),
                                         'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-400 border-orange-300 dark:border-orange-900': (inherentLevelName === 'Mod High' || inherentLevelName === 'Moderate to High'),
                                         'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-900': inherentLevelName === 'Moderate',
                                         'bg-lime-100 dark:bg-lime-950/80 text-lime-800 dark:text-lime-300 border-lime-300 dark:border-lime-900': (inherentLevelName === 'Low Mod' || inherentLevelName === 'Low to Moderate'),
                                         'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-900': (inherentLevelName === 'Low' || inherentLevelName === 'Rendah'),
                                         'bg-slate-50 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700': !inherentLevelName
                                     }">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full"
                                              :class="{
                                                  'bg-rose-600': (inherentLevelName === 'High' || inherentLevelName === 'Tinggi'),
                                                  'bg-orange-500': (inherentLevelName === 'Mod High' || inherentLevelName === 'Moderate to High'),
                                                  'bg-amber-500': inherentLevelName === 'Moderate',
                                                  'bg-lime-500': (inherentLevelName === 'Low Mod' || inherentLevelName === 'Low to Moderate'),
                                                  'bg-emerald-600': (inherentLevelName === 'Low' || inherentLevelName === 'Rendah'),
                                                  'hidden': !inherentLevelName
                                              }"></span>
                                        <span x-text="inherentLevelName || '-'"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Nilai Target --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-900 dark:text-slate-100 mb-2">Nilai Target <span class="text-rose-500">*</span></label>
                                <input type="number"
                                     name="target_value"
                                     x-model="targetValue"
                                     x-init="targetValue = '{{ old('target_value', $risk->target_value) }}'"
                                     min="1" max="25"
                                     placeholder="1 - 25"
                                     value="{{ old('target_value', $risk->target_value) }}"
                                     class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs text-slate-800 dark:text-slate-200 outline-none h-[38px]">
                                <input type="hidden"
                                     name="target_id_level"
                                     :value="otomatisTargetLevel || '{{ old('target_id_level', $risk->target_id_level) }}'">
                            </div>

                            {{-- Level Target (auto) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-900 dark:text-slate-100 mb-2">Level Target</label>
                                <div class="flex h-[38px] items-center justify-center rounded-xl border px-3 text-xs font-bold transition-all"
                                     :class="{
                                         'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-400 border-rose-300 dark:border-rose-900': (otomatisTargetLevelName === 'High' || otomatisTargetLevelName === 'Tinggi'),
                                         'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-400 border-orange-300 dark:border-orange-900': (otomatisTargetLevelName === 'Mod High' || otomatisTargetLevelName === 'Moderate to High'),
                                         'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-900': otomatisTargetLevelName === 'Moderate',
                                         'bg-lime-100 dark:bg-lime-950/80 text-lime-800 dark:text-lime-300 border-lime-300 dark:border-lime-900': (otomatisTargetLevelName === 'Low Mod' || otomatisTargetLevelName === 'Low to Moderate'),
                                         'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-900': (otomatisTargetLevelName === 'Low' || otomatisTargetLevelName === 'Rendah'),
                                         'bg-slate-50 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700': !otomatisTargetLevelName
                                     }">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full"
                                              :class="{
                                                  'bg-rose-600': (otomatisTargetLevelName === 'High' || otomatisTargetLevelName === 'Tinggi'),
                                                  'bg-orange-500': (otomatisTargetLevelName === 'Mod High' || otomatisTargetLevelName === 'Moderate to High'),
                                                  'bg-amber-500': otomatisTargetLevelName === 'Moderate',
                                                  'bg-lime-500': (otomatisTargetLevelName === 'Low Mod' || otomatisTargetLevelName === 'Low to Moderate'),
                                                  'bg-emerald-600': (otomatisTargetLevelName === 'Low' || otomatisTargetLevelName === 'Rendah'),
                                                  'hidden': !otomatisTargetLevelName
                                              }"></span>
                                        <span x-text="otomatisTargetLevelName || '-'"></span>
                                    </span>
                                </div>
                            </div>

                        </div>

                            {{-- TOMBOL AKSI --}}
                            <div class="flex items-center justify-end gap-2 pt-1">
                                {{-- Tombol Batal --}}
                                <button type="reset"
                                        class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
                                    Batal
                                </button>

                                {{-- Tombol Simpan --}}
                                <button type="submit"
                                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700">
                                    Simpan
                                </button>
                            </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>



    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.querySelector('textarea[name="risk_event_deta"]');
            const charCount = document.getElementById('charCount');
            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;
                textarea.addEventListener('input', function () {
                    charCount.textContent = this.value.length;
                });
            }
        });
    </script>
</x-admin-layout>
