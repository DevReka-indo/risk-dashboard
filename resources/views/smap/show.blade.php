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
                <h1 class="text-base font-bold text-slate-900">Detail & Monitoring Risk SMAP</h1>
                <p class="text-xs text-slate-500">Kelola input nilai berkala dan riwayat perkembangan risiko</p>
            </div>
        </div>
    </x-slot>

    @php
        $historyData = [];
        foreach($risk->detailPeriode as $p) {
            $historyData[$p->year][$p->quarter] = $p->value;
        }
        $targetId = $risk->id_level_target ?? null;
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
        <div style="display:flex; gap:8px;">
            <a href="{{ route('smap-risk.edit', $risk->id_smap) }}"
               style="border:1px solid #e2e8f0; border-radius:8px; padding:7px 18px; font-size:13px; font-weight:600; color:#475569; background:#fff; text-decoration:none; display:inline-block; transition:all 0.2s;"
               onmouseover="this.style.background='#f8fafc';"
               onmouseout="this.style.background='#fff';">
                Edit
            </a>
            <form method="POST" action="{{ route('smap-risk.destroy', $risk->id_smap) }}"
                  onsubmit="return confirm('Yakin hapus?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="border:1px solid #fca5a5; border-radius:8px; padding:7px 18px; font-size:13px; font-weight:600; color:#ef4444; background:#fff; cursor:pointer; transition:all 0.2s;"
                        onmouseover="this.style.background='#fef2f2';"
                        onmouseout="this.style.background='#fff';">
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
            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Status</p>
                @if($risk->status)
                    <span style="background:#d1fae5; color:#065f46; border-radius:12px; padding:8px 20px; font-size:16px; font-weight:700; display:inline-block;">Aktif</span>
                @else
                    <span style="background:#f1f5f9; color:#94a3b8; border-radius:12px; padding:8px 20px; font-size:16px; font-weight:700; display:inline-block;">Tidak Aktif</span>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: 1 Kotak Peristiwa Resiko --}}
        <div style="flex:2;">
            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc; height:100%; display:flex; flex-direction:column;">
                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Peristiwa Resiko</p>
                <p style="font-size:13px; color:#475569; line-height:1.8; margin:0; text-align:justify; flex:1;">
                    {{ $risk->risk_event_deta ?? '-' }}
                </p>
                <div style="margin-top:12px; padding-top:10px; border-top:1px solid #e2e8f0;">
                    <p style="font-size:10px; color:#94a3b8; margin:0;">Dibuat : {{ $risk->created_at?->translatedFormat('d F Y H.i') ?? '-' }}</p>
                </div>
            </div>
        </div>

    </div>
</div>

        {{-- ═══════════ CARD 2: Input Parameter Risiko Per Triwulan ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"
             x-data="smapRiskForm(@js($historyData), '{{ old('year', date('Y')) }}', '{{ $risk->inherent }}', '{{ $risk->inherent_target }}')">

            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Input Parameter Risiko Per Triwulan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 20px;">Perbarui nilai Current, Status, dan Penanganan untuk Triwulan ini</p>

            <form method="POST" action="{{ route('smap-risk.store-monitoring', $risk->id_smap) }}">
                @csrf

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
                                <p style="font-size:14px; font-weight:700; color:#f59e0b; margin:0;" x-text="inherentLevelName || '-'"></p>
                            </div>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Nilai Target</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->inherent_target ?? '-' }}</p>
                            </div>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Level Target</p>
                                <p style="font-size:14px; font-weight:700; color:#f59e0b; margin:0;" x-text="otomatisTargetLevelName || '-'"></p>
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
                                    <select name="quarter" x-model="quarter"
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
                                    <input type="number" name="year" x-model="year"
                                           style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box;">
                                </div>
                            </div>
                        </div>

                        {{-- Baris 2: Nilai saat ini + Status Penanganan + Status Monitoring --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Nilai saat ini</label>
                                <div style="position:relative;">
                                    <select name="value_select" x-model="value"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#94a3b8; background:#fff; outline:none;">
                                        <option value="">Masukkan nilai 1-25</option>
                                        @for($i = 1; $i <= 25; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <input type="hidden" name="value" :value="value">
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Status Penanganan</label>
                                <div style="position:relative;">
                                    <select name="status_penanganan"
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none;">
                                        <option value="belum">Belum</option>
                                        <option value="proses">Proses</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
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
                        </div>

                        {{-- Baris 3: Nilai saat ini (level badge) + Trend --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
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

                        {{-- Tombol --}}
                        <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:4px;">
                            <button type="reset"
                                    style="border:1px solid #e2e8f0; border-radius:8px; padding:8px 20px; font-size:13px; font-weight:600; color:#475569; background:#fff; cursor:pointer; transition:all 0.2s;"
                                    onmouseover="this.style.background='#f8fafc';"
                                    onmouseout="this.style.background='#fff';">
                                Batal
                            </button>
                            <button type="submit"
                                    style="background:#4F7EF0; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s;"
                                    onmouseover="this.style.background='#3b66d9';"
                                    onmouseout="this.style.background='#4F7EF0';">
                                Simpan
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

          {{-- ═══════════ CARD 3: Riwayat monitoring Triwulan ═══════════ --}}
        <div>
            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Riwayat monitoring Triwulan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 16px;">Daftar riwayat yang telah dimasukkan untuk risiko ini</p>

            <div style="display:flex; flex-direction:column; gap:16px;">
                @forelse($risk->detailPeriode as $history)
                    @php
                        $lvl = strtolower($history->levelRisiko->nama_level ?? $history->levelRisiko->level ?? '');
                        $lvlStyle = match($lvl) {
                            'high' => 'background:#fef2f2;color:#ef4444;',
                            'moderate to high' => 'background:#fff7ed;color:#f97316;',
                            'moderate' => 'background:#fffbeb;color:#f59e0b;',
                            'low to moderate' => 'background:#eff6ff;color:#3b82f6;',
                            'low' => 'background:#ecfdf5;color:#10b981;',
                            default => 'background:#f1f5f9;color:#64748b;',
                        };
                        $pen = $history->status_penanganan ?? 'Belum';
                        $penDisplay = match($pen) {
                            'selesai' => 'Selesai',
                            'proses' => 'Proses',
                            default => 'Belum',
                        };
                        $penStyle = match($pen) {
                            'selesai' => 'background:#ecfdf5;color:#10b981;',
                            'proses' => 'background:#eff6ff;color:#3b82f6;',
                            default => 'background:#f1f5f9;color:#64748b;',
                        };
                    @endphp

                    <div x-data="{ editOpen: false }"
                        style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">

                        {{-- Header Card --}}
                        <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <span style="background:#1e293b; color:#fff; border-radius:20px; padding:4px 14px; font-size:13px; font-weight:700;">
                                        @php
                                            $displayQ = $history->quarter;
                                            if (is_numeric($displayQ)) {
                                                $displayQ = 'TW' . $displayQ;
                                            } elseif (str_contains($displayQ, 'Q')) {
                                                $displayQ = str_replace('Q', 'TW', $displayQ);
                                            }
                                        @endphp
                                        {{ $displayQ }} {{ $history->year }}
                                    </span>
                                    <span style="background:#eff6ff; color:#4f46e5; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">Nilai {{ $history->value ?? '-' }}</span>
                                    <span style="{{ $lvlStyle }} border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">{{ ucfirst($history->levelRisiko->nama_level ?? $history->levelRisiko->level ?? '-') }}</span>
                                    <span style="{{ $penStyle }} border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">{{ $penDisplay }}</span>

                                    {{-- Badge Tambahan Status Keaktifan Master Risiko --}}
                                    <span style="{{ ($risk->status ?? 1) == 1 ? 'background:#ecfdf5;color:#10b981;' : 'background:#fef2f2;color:#ef4444;' }} border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        Status: {{ ($risk->status ?? 1) == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <button type="button" @click="editOpen = !editOpen"
                                            style="font-size:12px; font-weight:600; color:#475569; background:none; border:none; cursor:pointer; transition:color 0.2s; padding:4px 8px;"
                                            onmouseover="this.style.color='#1e293b';"
                                            onmouseout="this.style.color='#475569';">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('smap-risk.destroy-monitoring', $history->id_detail) }}"
                                        onsubmit="return confirm('Hapus periode ini?')" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                style="font-size:12px; font-weight:600; color:#ef4444; background:none; border:none; cursor:pointer; transition:color 0.2s; padding:4px 8px;"
                                                onmouseover="this.style.color='#dc2626';"
                                                onmouseout="this.style.color='#ef4444';">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Body: 4 Kotak Info --}}
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; padding:16px 20px;">
                            {{-- Kotak 1: Nilai Inheren --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Nilai Inheren</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->inherent ?? '-' }}</p>
                            </div>

                            {{-- Kotak 2: Nilai Target --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Nilai Target</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $risk->inherent_target ?? '-' }}</p>
                            </div>

                            {{-- Kotak 3: Penanganan --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Penanganan</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $penDisplay }}</p>
                            </div>

                            {{-- Kotak 4: Tren Perubahan --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase; letter-spacing:0.3px;">Tren Perubahan</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">
                                    @if(($history->trend ?? '') === 'Naik')
                                        <span style="color:#ef4444;">↑ Naik</span>
                                    @elseif(($history->trend ?? '') === 'Turun')
                                        <span style="color:#10b981;">↓ Turun</span>
                                    @else
                                        <span style="color:#94a3b8;">→ Stabil</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Edit Form (collapse) --}}
                        <div x-show="editOpen" x-transition style="display:none; border-top:1px solid #f1f5f9; padding:16px 20px; background:#fafbfc;">
                            <form method="POST" action="{{ route('smap-risk.update-monitoring', $history->id_detail) }}">
                                @csrf @method('PUT')
                                {{-- Mengubah grid-template-columns menjadi repeat(5,1fr) agar muat berjejer seimbang --}}
                                <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:12px;">
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Kuartal</label>
                                        <select name="quarter" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff; font-weight:600;">
                                            @php
                                                $currentQ = $history->quarter;
                                                if (is_numeric($currentQ)) {
                                                    $currentQ = 'TW' . $currentQ;
                                                } elseif (str_contains($currentQ, 'Q')) {
                                                    $currentQ = str_replace('Q', 'TW', $currentQ);
                                                }
                                            @endphp
                                            <option value="TW1" {{ $currentQ == 'TW1' ? 'selected' : '' }}>TW1</option>
                                            <option value="TW2" {{ $currentQ == 'TW2' ? 'selected' : '' }}>TW2</option>
                                            <option value="TW3" {{ $currentQ == 'TW3' ? 'selected' : '' }}>TW3</option>
                                            <option value="TW4" {{ $currentQ == 'TW4' ? 'selected' : '' }}>TW4</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Tahun</label>
                                        <input type="number" name="year" min="2020" max="2099"
                                            value="{{ old('edit_year_'.$history->id_detail, $history->year ?? date('Y')) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff; font-weight:600;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Nilai Current</label>
                                        <input type="number" name="value" min="1" max="25"
                                            value="{{ old('edit_value_'.$history->id_detail, $history->value) }}"
                                            style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff; font-weight:700;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Penanganan</label>
                                        <select name="status_penanganan" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff;">
                                            <option value="belum" @selected(($history->status_penanganan ?? 'belum') == 'belum')> Belum</option>
                                            <option value="proses" @selected(($history->status_penanganan ?? '') == 'proses')> Proses</option>
                                            <option value="selesai" @selected(($history->status_penanganan ?? '') == 'selesai')> Selesai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Status Risiko</label>
                                        <select name="status" required style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff;">
                                            <option value="1" {{ ($risk->status ?? 1) == 1 ? 'selected' : '' }}> Aktif</option>
                                            <option value="0" {{ ($risk->status ?? 1) == 0 ? 'selected' : '' }}> Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display:flex; justify-content:flex-end;">
                                    <button type="submit"
                                            style="background:#4F7EF0; border:none; border-radius:8px; padding:8px 22px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s;"
                                            onmouseover="this.style.background='#3b66d9';"
                                            onmouseout="this.style.background='#4F7EF0';">
                                        Simpan Perubahan
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

    <script src="{{ asset('js/smap-logic.js') }}?v={{ time() }}"></script>
</x-admin-layout>
