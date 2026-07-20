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
                <h1 class="text-base font-bold text-slate-900">Detail & Monitoring Risk Departemen</h1>
                <p class="text-xs text-slate-500">Kelola input nilai berkala dan riwayat perkembangan risiko</p>
            </div>
        </div>
    </x-slot>

    @php
        $historyData = [];
        foreach($risk->periods as $p) {
            $historyData[$p->pivot->year][$p->pivot->quarter] = $p->pivot->value;
        }
        $targetId = $risk->target_id_level ?? null;
        $targetName = match((int) $targetId) {
            1 => 'Low', 2 => 'Low to Moderate', 3 => 'Moderate', 4 => 'Moderate to High', 5 => 'High',
            default => '-'
        };
    @endphp

    <div class="space-y-5">

        {{-- Flash --}}
        @if(session('success'))
            <div style="border:1px solid #6ee7b7; background:#ecfdf5; border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; font-weight:600;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b; font-weight:600;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- ═══════════ CARD 1: Detail Lengkap ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">

            {{-- Header Card 1 --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Detail Lengkap</h2>
                    <p style="font-size:12px; color:#94a3b8; margin:0;">Seluruh informasi data risiko ini</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Tombol Edit --}}
                    <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>

                    {{-- Tombol Hapus --}}
                    <form method="POST" action="{{ route('department-risk.destroy', $risk->id_monitoring) }}"
                        onsubmit="return confirm('Yakin hapus?')" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Isi Card 1 --}}
            <div style="display:flex; gap:24px;">

                {{-- Kolom Kiri: 3 Kotak --}}
                <div style="display:flex; flex-direction:column; gap:12px; flex:1;">
                    {{-- Kotak 1: Unit Kerja --}}
                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Unit Kerja</p>
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->unitKerja->nama_unit ?? '-' }}</p>
                    </div>

                    {{-- Kotak 2: Kategori --}}
                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Kategori</p>
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->kategoriRisiko->nama_kategori ?? '-' }}</p>
                    </div>

                    {{-- Kotak 3: Status --}}
                    <div class="border border-slate-200 rounded-[8px] px-4 py-3 bg-[#fafbfc]">
                        <p class="text-[10px] font-semibold text-slate-400 m-0 mb-1 uppercase tracking-[0.3px]">Status</p>
                        @if($risk->status)
                            <span class="inline-flex rounded bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex rounded bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan: 1 Kotak Peristiwa Resiko --}}
                <div style="flex:4;">
                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc; height:100%; display:flex; flex-direction:column;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Peristiwa Resiko</p>
                        <p style="font-size:13px; color:#475569; line-height:1.8; margin:0; text-align:justify; flex:1;">
                            {{ $risk->risk_event_deta ?? '-' }}
                        </p>
                        <div style="margin-top:12px; padding-top:10px; border-top:1px solid #e2e8f0;">
                            <p style="font-size:11px; color:#94a3b8; margin:0;">Dibuat : {{ $risk->created_at?->translatedFormat('d F Y H.i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════ CARD 2: Input Parameter Risiko Per Triwulan ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"
             x-data="smapRiskForm(@js($historyData), '{{ old('year', date('Y')) }}', '{{ $risk->inherent }}', '{{ $risk->target_value }}')">

            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Input Parameter Risiko Per Triwulan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 20px;">Perbarui nilai Current, Status, dan Penanganan untuk Triwulan ini</p>

            <form method="POST" action="{{ route('department-risk.update-period', $risk->id_monitoring) }}">
                @csrf @method('PUT')

                {{-- HIDDEN INPUT: Ditambahkan agar lolos validasi Required dari backend --}}
                <input type="hidden" name="inherent" value="{{ $risk->inherent }}">
                <input type="hidden" name="id_level" value="{{ $risk->id_level }}">
                <input type="hidden" name="target_value" value="{{ $risk->target_value }}">
                <input type="hidden" name="target_id_level" value="{{ $risk->target_id_level }}">

                <div style="display:grid; grid-template-columns:200px 1fr; gap:20px; align-items:start;">

                    {{-- Kiri: Parameter Awal & Target (Read Only) --}}
                    <div style="border:1px solid #e2e8f0; border-radius:12px; padding:16px; background:#fafafa;">
                        <p style="font-size:12px; font-weight:700; color:#1e293b; margin:0 0 4px;">Parameter Awal dan Target</p>
                        <p style="font-size:11px; color:#94a3b8; margin:0 0 14px;">Acuan perkembangan statistik (Read Only)</p>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Nilai Inheren</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->inherent ?? '-' }}</p>
                            </div>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Level Inheren</p>
                                <p style="font-size:14px; font-weight:700; color:#f59e0b; margin:0;">{{ $risk->levelRisiko->nama_level ?? '-' }}</p>
                            </div>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Nilai Target</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->target_value ?? '-' }}</p>
                            </div>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Level Target</p>
                                <p style="font-size:14px; font-weight:700; color:#f59e0b; margin:0;">{{ $risk->targetLevel->nama_level ?? $targetName }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: Form Input --}}
                    <div style="display:flex; flex-direction:column; gap:16px;">

                        {{-- Baris 1: Triwulan + Tahun --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Triwulan</label>
                                <div style="position:relative;">
                                    <select name="quarter" x-model="quarter" x-init="if('{{ old('quarter') }}') quarter = '{{ old('quarter') }}'"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none;">
                                        <option value="TW1">TW1</option>
                                        <option value="TW2">TW2</option>
                                        <option value="TW3">TW3</option>
                                        <option value="TW4">TW4</option>
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Tahun</label>
                                <div style="position:relative;">
                                    <input type="number" name="year" x-model="year" x-init="if('{{ old('year') }}') year = '{{ old('year') }}'"
                                           style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box;">
                                </div>
                            </div>
                        </div>

                        {{-- Baris 2: Nilai saat ini (input number) + Status Penanganan (3 input number) --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Nilai saat ini (1-25)</label>
                                <input type="number" name="value" x-model="value" min="1" max="25"
                                       placeholder="1 - 25"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Progres Belum</label>
                                <input type="number" name="progres_belum" value="{{ old('progres_belum', 0) }}" min="0"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Progres Proses</label>
                                <input type="number" name="progres_proses" value="{{ old('progres_proses', 0) }}" min="0"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Progres Sudah</label>
                                <input type="number" name="progres_sudah" value="{{ old('progres_sudah', 0) }}" min="0"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                            </div>
                        </div>

                        {{-- Baris 3: Status Monitoring + Level Badge + Trend --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Status Monitoring</label>
                                <div style="position:relative;">
                                    <select name="status_monitoring"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none;">
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Nilai saat ini</label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; min-height:40px; background:#fff; display:flex; align-items:center;">
                                    <span x-show="otomatisLevel" style="display:none;">
                                        <span x-text="{1:'Low',2:'Low to Moderate',3:'Moderate',4:'Moderate to High',5:'High'}[otomatisLevel]"
                                              :style="{
                                                1:'background:#ecfdf5;color:#10b981',
                                                2:'background:#eff6ff;color:#3b82f6',
                                                3:'background:#fffbeb;color:#f59e0b',
                                                4:'background:#fff7ed;color:#f97316',
                                                5:'background:#fef2f2;color:#ef4444'
                                              }[otomatisLevel]"
                                              style="border-radius:6px; padding:3px 10px; font-size:12px; font-weight:700;"></span>
                                    </span>
                                    <input type="hidden" name="calculated_level" :value="otomatisLevel">
                                </div>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Trend Perubahan</label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; min-height:40px; background:#fff; display:flex; align-items:center;">
                                    <span x-text="otomatisTrend || ''"
                                          :style="otomatisTrend === 'Naik' ? 'color:#10b981;font-weight:700;font-size:14px;' : otomatisTrend === 'Turun' ? 'color:#ef4444;font-weight:700;font-size:14px;' : 'color:#94a3b8;font-size:13px;'"></span>
                                    <input type="hidden" name="calculated_trend" :value="otomatisTrend">
                                </div>
                            </div>
                        </div>

                    {{-- Tombol Form (Batal & Simpan) --}}
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

                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════ CARD 3: Riwayat Monitoring Triwulan ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">

            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Riwayat monitoring Triwulan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 20px;">Daftar riwayat yang telah dimasukkan untuk risiko ini</p>

            <div style="display:flex; flex-direction:column; gap:12px;">
                @forelse($risk->periods as $period)
                    @php
                        $lvl = strtolower($period->nama_level ?? $period->level ?? '');
                        $lvlStyle = match($lvl) {
                            'high' => 'background:#fef2f2;color:#ef4444;',
                            'moderate to high' => 'background:#fff7ed;color:#f97316;',
                            'moderate' => 'background:#fffbeb;color:#f59e0b;',
                            'low to moderate' => 'background:#eff6ff;color:#3b82f6;',
                            'low' => 'background:#ecfdf5;color:#10b981;',
                            default => 'background:#f1f5f9;color:#64748b;',
                        };

                        $pBelum  = $period->pivot->progres_belum ?? 0;
                        $pProses = $period->pivot->progres_proses ?? 0;
                        $pSudah  = $period->pivot->progres_sudah ?? 0;

                        $statusStyle = ($risk->status ?? 1) == 1
                            ? 'background:#ecfdf5;color:#10b981;'
                            : 'background:#fef2f2;color:#ef4444;';

                        $trend = $period->pivot->trend ?? 'Stabil';
                        $trendIcon = match($trend) {
                            'Naik' => '↑',
                            'Turun' => '↓',
                            default => '→',
                        };
                        $trendColor = match($trend) {
                            'Naik' => '#10b981',
                            'Turun' => '#ef4444',
                            default => '#94a3b8',
                        };
                    @endphp

                    <div x-data="{ editOpen: false }"
                        style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">

                        {{-- Row 1: Badge Info --}}
                        <div style="padding:12px 20px; border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    {{-- TW --}}
                                    <span style="background:#1e293b; color:#fff; border-radius:8px; padding:4px 14px; font-size:13px; font-weight:700;">
                                        {{ $period->pivot->quarter }} {{ $period->pivot->year }}
                                    </span>

                                    {{-- Nilai --}}
                                    <span style="background:#eff6ff; color:#4f46e5; border-radius:8px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        Nilai {{ $period->pivot->value ?? '-' }}
                                    </span>

                                    {{-- Level --}}
                                    <span style="{{ $lvlStyle }} border-radius:8px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        {{ ucfirst($period->nama_level ?? $period->level ?? '-') }}
                                    </span>

                                    {{-- Progres --}}
                                    <span style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; border-radius:8px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        Progres (Belum: <span style="color:#64748b;">{{ $pBelum }}</span> | Proses: <span style="color:#3b82f6;">{{ $pProses }}</span> | Sudah: <span style="color:#10b981;">{{ $pSudah }}</span>)
                                    </span>

                                    {{-- Status --}}
                                    <span style="{{ $statusStyle }} border-radius:8px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        Status: {{ ($risk->status ?? 1) == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>


                                {{-- Tombol Edit & Hapus --}}
                                <div class="flex items-center gap-2">
                                    {{-- Tombol Edit (Toggle Alpine.js) --}}
                                    <button type="button" @click="editOpen = !editOpen"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>

                                    {{-- Tombol Hapus Periode --}}
                                    <form method="POST" action="{{ route('department-risk.destroy-period', [$risk->id_monitoring, $period->pivot->id]) }}"
                                        onsubmit="return confirm('Hapus periode ini?')" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: 4 Kotak Info --}}
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; padding:16px 20px;">
                            {{-- Kotak 1: Nilai Inheren --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Nilai Inheren</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $period->pivot->inherent ?? $risk->inherent ?? '-' }}</p>
                            </div>

                            {{-- Kotak 2: Nilai Target --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Nilai Target</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $period->pivot->target_value ?? $risk->target_value ?? '-' }}</p>
                            </div>

                            {{-- Kotak 3: Statistik Progres --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Statistik Progres (B/P/S)</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">
                                    <span style="color:#64748b;">{{ $pBelum }}</span> /
                                    <span style="color:#3b82f6;">{{ $pProses }}</span> /
                                    <span style="color:#10b981;">{{ $pSudah }}</span>
                                </p>
                            </div>

                            {{-- Kotak 4: Tren Perubahan --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Tren Perubahan</p>
                                <p style="font-size:16px; font-weight:700; color:{{ $trendColor }}; margin:0;">
                                    {{ $trendIcon }} {{ $trend }}
                                </p>
                            </div>
                        </div>

                        {{-- Edit Form (collapse) --}}
                        <div x-show="editOpen" x-transition style="display:none; border-top:1px solid #f1f5f9; padding:16px 20px; background:#fafbfc;">
                            <form method="POST" action="{{ route('department-risk.update-existing-period', [$risk->id_monitoring, $period->pivot->id]) }}">
                                @csrf @method('PUT')

                                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px 14px; margin-bottom:12px;">
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Kuartal</label>
                                        <select name="quarter" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff;">
                                            <option value="TW1" {{ $period->pivot->quarter == 'TW1' ? 'selected' : '' }}>TW1</option>
                                            <option value="TW2" {{ $period->pivot->quarter == 'TW2' ? 'selected' : '' }}>TW2</option>
                                            <option value="TW3" {{ $period->pivot->quarter == 'TW3' ? 'selected' : '' }}>TW3</option>
                                            <option value="TW4" {{ $period->pivot->quarter == 'TW4' ? 'selected' : '' }}>TW4</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Tahun</label>
                                        <input type="number" name="year" min="2020" max="2099"
                                            value="{{ old('edit_year_'.$period->pivot->id, $period->pivot->year ?? date('Y')) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Nilai</label>
                                        <input type="number" name="value" min="1" max="25"
                                            value="{{ old('edit_value_'.$period->pivot->id, $period->pivot->value) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Belum</label>
                                        <input type="number" name="progres_belum" min="0"
                                            value="{{ old('edit_progres_belum_'.$period->pivot->id, $pBelum) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Proses</label>
                                        <input type="number" name="progres_proses" min="0"
                                            value="{{ old('edit_progres_proses_'.$period->pivot->id, $pProses) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Sudah</label>
                                        <input type="number" name="progres_sudah" min="0"
                                            value="{{ old('edit_progres_sudah_'.$period->pivot->id, $pSudah) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                </div>

                                {{-- 👇 PERBAIKAN: Menggunakan value statis dari backend sebagai gantinya 👇 --}}
                                <input type="hidden" name="calculated_level" value="{{ $period->nama_level ?? $period->level ?? $risk->levelRisiko->nama_level }}">
                                <input type="hidden" name="calculated_trend" value="{{ $trend }}">

                            {{-- Tombol Form (Batal & Simpan) --}}
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
                            </form>
                        </div>

                    </div>
                @empty
                    <div style="border:1px dashed #e2e8f0; border-radius:14px; padding:32px; text-align:center; color:#94a3b8; font-size:13px;">
                        Belum ada riwayat triwulan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <script src="{{ asset('js/otomatisasi-logic.js') }}?v={{ time() }}"></script>
</x-admin-layout>
