<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('smap-risk.index') }}"
               class="flex h-7 w-7 items-center justify-center rounded text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Tambah Risk SMAP</h1>
                <p class="text-xs text-slate-500">Daftarkan risiko SMAP baru ke dalam sistem</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('smap-risk.store') }}" x-data="smapRiskForm({}, '', '{{ old('inherent', 0) }}', '{{ old('inherent_target', 0) }}')">
        @csrf

        {{-- CARD UTAMA --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- TABEL: BARIS ATAS --}}
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    {{-- KOLOM KIRI --}}
                    <td style="width:270px; padding:24px; border-right:1px solid #f1f5f9; vertical-align:top; background:#fafbfc;">
                        <div style="display:flex; flex-direction:column; gap:18px;">

                            {{-- Unit Kerja --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Unit Kerja <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <select name="id_unit"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; cursor:pointer;">
                                        <option value="" disabled selected>Pilih Unit Kerja</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id_unit }}" @selected(old('id_unit') == $unit->id_unit)>{{ $unit->nama_unit }}</option>
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
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Kategori <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="position:relative;">
                                    <select name="id_kategori"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; cursor:pointer;">
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id_kategori }}" @selected(old('id_kategori') == $cat->id_kategori)>{{ $cat->nama_kategori }}</option>
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
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Tanggal Dibuat <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="date" name="created_at" value="{{ old('created_at', date('Y-m-d')) }}"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box;">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Status <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; background:#fff;">
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="radio" name="status" value="1" style="accent-color:#4F7EF0;" @checked(old('status', '1') === '1')>
                                            <span style="font-size:13px; color:#475569;">Aktif</span>
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="radio" name="status" value="0" style="accent-color:#4F7EF0;" @checked(old('status') === '0')>
                                            <span style="font-size:13px; color:#475569;">Non-Aktif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>

                    {{-- KOLOM KANAN --}}
                    <td style="padding:24px; vertical-align:top;">
                        <div style="display:flex; flex-direction:column; height:100%;">
                            <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                Peristiwa Resiko <span style="color:#ef4444;">*</span>
                            </label>
                            <textarea name="risk_event_deta" placeholder="Masukkan peristiwa resiko secara detail..."
                                      style="flex:1; width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px; font-size:13px; color:#475569; background:#fff; resize:none; outline:none; min-height:240px; font-family:inherit; line-height:1.6;">{{ old('risk_event_deta') }}</textarea>
                            <div style="margin-top:6px; font-size:11px; color:#94a3b8; text-align:right;">
                                <span id="charCount">0</span> karakter
                            </div>
                            @error('risk_event_deta') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                        </div>
                    </td>
                </tr>
            </table>

            {{-- DIVIDER --}}
            <div style="border-top:2px solid #f1f5f9;"></div>

            {{-- BARIS BAWAH (Nilai & Level) --}}
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:24px;">
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px;">

                            {{-- Nilai Inheren --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Nilai Inheren <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="number" name="inherent" x-model="inherent" min="1" max="25" placeholder="1 - 25"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                <input type="hidden" name="id_level" :value="inherentLevel">
                            </div>

                            {{-- Level Inheren --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Level Inheren
                                </label>
                                <div style="border:2px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#1e293b; background:#f8fafc; text-align:center; height:38px; display:flex; align-items:center; justify-content:center; font-weight:600; box-sizing:border-box;">
                                    <span x-text="inherentLevelName || ''"></span>
                                </div>
                            </div>

                            {{-- Nilai Target --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Nilai Target <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="number" name="inherent_target" x-model="targetValue" min="1" max="25" placeholder="1 - 25"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                <input type="hidden" name="id_level_target" :value="otomatisTargetLevel">
                            </div>

                            {{-- Level Target --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Level Target
                                </label>
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
            <a href="{{ route('smap-risk.index') }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
            Batal
            </a>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700">
            Tambah
            </button>
        </div>

    </form>

    {{-- DIBETULKAN KETIK MEMANGGIL JS --}}
    <script src="{{ asset('js/smap-logic.js') }}?v={{ time() }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[name="risk_event_deta"]');
            const charCount = document.getElementById('charCount');
            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;
                textarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
        });
    </script>
</x-admin-layout>
