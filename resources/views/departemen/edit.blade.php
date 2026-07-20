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
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Level Inheren</label>
                                <div style="border:2px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#1e293b; background:#f8fafc; text-align:center; height:38px; display:flex; align-items:center; justify-content:center; font-weight:600; box-sizing:border-box;">
                                    <span x-text="inherentLevelName || ''"></span>
                                </div>
                            </div>

                            {{-- Nilai Target --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Nilai Target <span style="color:#ef4444;">*</span></label>

                                {{-- MENGGUNAKAN targetValue --}}
                               <input type="number"
                                    name="target_value"
                                    x-model="targetValue"
                                    x-init="targetValue = '{{ old('target_value', $risk->target_value) }}'"
                                    min="1" max="25"
                                    placeholder="1 - 25"
                                    value="{{ old('target_value', $risk->target_value) }}"
                                    style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">

                                {{-- MENGGUNAKAN otomatisTargetLevel --}}
                                <input type="hidden"
                                    name="target_id_level"
                                    :value="otomatisTargetLevel || '{{ old('target_id_level', $risk->target_id_level) }}'">
                            </div>

                            {{-- Level Target (auto) --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Level Target</label>
                                <div style="border:2px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#1e293b; background:#f8fafc; text-align:center; height:38px; display:flex; align-items:center; justify-content:center; font-weight:600; box-sizing:border-box;">
                                    <span x-text="otomatisTargetLevelName || ''"></span>
                                </div>
                            </div>

                        </div>
                    </td>
                </tr>
            </table>

        </div>

        {{-- TOMBOL AKSI --}}
        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:12px; padding:0 4px;">
            <a href="{{ route('department-risk.index') }}"
               style="border:1px solid #e2e8f0; border-radius:10px; padding:10px 28px; font-size:13px; font-weight:600; color:#475569; background:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s;"
               onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';"
               onmouseout="this.style.background='#fff'; this.style.borderColor='#e2e8f0';">
                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit"
                    style="background:#4F7EF0; border:none; border-radius:10px; padding:10px 32px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(79,126,240,0.3);"
                    onmouseover="this.style.background='#3b66d9'; this.style.boxShadow='0 6px 16px rgba(79,126,240,0.4)';"
                    onmouseout="this.style.background='#4F7EF0'; this.style.boxShadow='0 4px 12px rgba(79,126,240,0.3)';">
                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Risk Departemen
            </button>
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
