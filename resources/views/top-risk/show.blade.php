<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('top-risk.index') }}"
               class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Detail & Monitoring Top Risk</h1>
                <p class="text-xs text-slate-500">Kelola input nilai berkala dan riwayat perkembangan risiko</p>
            </div>
        </div>
    </x-slot>

    @php
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        function getLevelName($id) {
            $levels = [
                1 => 'Low',
                2 => 'Low to Moderate',
                3 => 'Moderate',
                4 => 'Moderate to High',
                5 => 'High'
            ];
            return $levels[$id] ?? '-';
        }
    @endphp

    <div class="space-y-5">

        {{-- Flash Message --}}
        @if(session('success'))
            <div style="border:1px solid #46c290ff; background:#ecfdf5; border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; font-weight:600;">{{ session('success') }}</div>
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

        {{-- ═══════════ CARD 1: Detail Lengkap Master Risiko ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Detail Lengkap</h2>
                    <p style="font-size:12px; color:#94a3b8; margin:0;">Seluruh informasi data risiko ini</p>
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('top-risk.edit', $topRisk) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('top-risk.destroy', $topRisk) }}"
                          onsubmit="return confirm('Yakin hapus data risiko ini?')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <div style="display:flex; gap:24px;">
                <div style="display:flex; flex-direction:column; gap:12px; flex:1;">
                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Kategori</p>
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">{{ $topRisk->kategori->nama_kategori ?? '-' }}</p>
                    </div>

                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Unit Kerja</p>
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">
                            @foreach ($topRisk->unitKerja as $unit)
                                <span style="display:inline-block; background:#f1f5f9; border-radius:6px; padding:2px 10px; margin:2px 4px 2px 0; font-size:12px;">{{ $unit->nama_unit }}</span>
                            @endforeach
                        </p>
                    </div>

                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Status Master</p>
                        @if($topRisk->is_aktif)
                            <span style="background:#ecfdf5; color:#10b981; border-radius:6px; padding:4px 14px; font-size:12px; font-weight:600; display:inline-block;">Aktif</span>
                        @else
                            <span style="background:#f1f5f9; color:#94a3b8; border-radius:6px; padding:4px 14px; font-size:12px; font-weight:600; display:inline-block;">Tidak Aktif</span>
                        @endif
                    </div>
                </div>

                <div style="flex:4;">
                    <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; background:#fafbfc; height:100%; display:flex; flex-direction:column;">
                        <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Nama Peristiwa Risiko</p>
                        <p style="font-size:13px; color:#475569; line-height:1.8; margin:0; text-align:justify; flex:1;">
                            {{ $topRisk->nama_peristiwa_risiko ?? '-' }}
                        </p>
                        <div style="margin-top:12px; padding-top:10px; border-top:1px solid #e2e8f0;">
                            <p style="font-size:10px; color:#94a3b8; margin:0;">Dibuat: {{ optional($topRisk->tanggal_dibuat)->translatedFormat('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ CARD 2: Form Input Monitoring Bulanan ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);"
             x-data="topRiskMonitoring({{ $topRisk->id_risiko }}, {{ $inherentAwal }})">

            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Input Monitoring Bulanan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 20px;">Perbarui nilai realisasi, status, dan progres penanganan untuk bulan ini</p>

            <form method="POST" action="{{ route('top-risk.monitoring.store', $topRisk) }}">
                @csrf

                <div style="display:grid; grid-template-columns:220px 1fr; gap:20px; align-items:start;">

                    {{-- KOTAK KIRI: Informasi Risiko (Dinamis Berdasarkan Bulan Dropdown) --}}
                    <div style="border:1px solid #e2e8f0; border-radius:12px; padding:16px; background:#fafafa;">
                        <p style="font-size:12px; font-weight:700; color:#1e293b; margin:0 0 4px;">Informasi Risiko</p>
                        <p style="font-size:11px; color:#94a3b8; margin:0 0 14px;">Acuan Baseline & Realisasi Lalu</p>

                        <div style="display:flex; flex-direction:column; gap:10px;">
                            {{-- Nilai Inheren Dinamis --}}
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
                                    <p style="font-size:11px; color:#94a3b8; margin:0;">Nilai Inheren</p>
                                    <span x-show="loadingInherent" style="font-size:10px; color:#4F7EF0;">Loading...</span>
                                </div>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;" x-text="inherentDisplay"></p>
                            </div>

                            {{-- Level Inheren Dinamis --}}
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 2px;">Level Inheren</p>
                                <p style="font-size:13px; font-weight:700; color:#f59e0b; margin:0;" x-text="levelDisplay"></p>
                            </div>

                            {{-- Skala Target TW1 - TW4 --}}
                            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; background:#fff;">
                                <p style="font-size:11px; color:#94a3b8; margin:0 0 6px;">Skala Target</p>
                                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:6px;">
                                    <div style="border:1px solid #e2e8f0; border-radius:6px; padding:6px 2px; text-align:center; background:#f8fafc;">
                                        <p style="font-size:9px; font-weight:600; color:#94a3b8; margin:0;">TW1</p>
                                        <p style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $topRisk->target_tw1 ?? '-' }}</p>
                                    </div>
                                    <div style="border:1px solid #e2e8f0; border-radius:6px; padding:6px 2px; text-align:center; background:#f8fafc;">
                                        <p style="font-size:9px; font-weight:600; color:#94a3b8; margin:0;">TW2</p>
                                        <p style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $topRisk->target_tw2 ?? '-' }}</p>
                                    </div>
                                    <div style="border:1px solid #e2e8f0; border-radius:6px; padding:6px 2px; text-align:center; background:#f8fafc;">
                                        <p style="font-size:9px; font-weight:600; color:#94a3b8; margin:0;">TW3</p>
                                        <p style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $topRisk->target_tw3 ?? '-' }}</p>
                                    </div>
                                    <div style="border:1px solid #e2e8f0; border-radius:6px; padding:6px 2px; text-align:center; background:#f8fafc;">
                                        <p style="font-size:9px; font-weight:600; color:#94a3b8; margin:0;">TW4</p>
                                        <p style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $topRisk->target_tw4 ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KANAN: Form Input Monitoring --}}
                    <div style="display:flex; flex-direction:column; gap:16px;">

                        {{-- Baris 1: Bulan + Tahun --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Bulan <span style="color:#ef4444;">*</span></label>
                                <div style="position:relative;">
                                    {{-- EVENT @change memicu perubahan Inherent Kiri secara instant --}}
                                    <select name="bulan" x-model="bulan" @change="fetchInherentPeriod()" required
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none;">
                                        <option value="" disabled>Pilih Bulan</option>
                                        @foreach ($monthNames as $monthNumber => $monthName)
                                            <option value="{{ $monthNumber }}" @selected((int) old('bulan', now()->month) === $monthNumber)>
                                                {{ $monthName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>

                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Tahun <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="tahun" x-model="tahun" @change="fetchInherentPeriod()" min="2000" required
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box;">
                            </div>
                        </div>

                        {{-- Baris 2: Nilai Realisasi + Level Realisasi Auto + Status --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Nilai Realisasi (1-25) <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="nilai" x-model="nilaiInput" min="1" max="25" required
                                       placeholder="1 - 25"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                            </div>

                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Level Realisasi</label>
                                <div style="border:2px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#1e293b; background:#f8fafc; text-align:center; height:38px; display:flex; align-items:center; justify-content:center; font-weight:600; box-sizing:border-box;">
                                    <span x-text="levelName || 'Otomatis'" :style="levelStyle"></span>
                                </div>
                                <input type="hidden" name="id_level" :value="levelId">
                            </div>

                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Status Monitoring <span style="color:#ef4444;">*</span></label>
                                <div style="position:relative;">
                                    <select name="status" required
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none;">
                                        <option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option>
                                        <option value="Tidak Aktif" @selected(old('status') === 'Tidak Aktif')>Tidak Aktif</option>
                                    </select>
                                    <svg style="position:absolute; right:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; pointer-events:none;" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Baris 3: Progres Belum + Proses + Sudah --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
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

                        {{-- Baris 4: Catatan --}}
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px;">Catatan</label>
                            <textarea name="catatan" placeholder="Catatan atau keterangan evaluasi bulanan..."
                                      style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; font-size:13px; color:#475569; background:#fff; resize:none; outline:none; min-height:60px; font-family:inherit; line-height:1.6;">{{ old('catatan') }}</textarea>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end gap-2 pt-1">
                        {{-- Tombol Batal --}}
                        <button type="reset"
                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700">
                            Simpan
                        </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

       {{-- ═══════════ CARD 3: Riwayat Monitoring Bulanan ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Riwayat Monitoring Bulanan</h2>
            <p style="font-size:12px; color:#94a3b8; margin:0 0 20px;">Daftar riwayat yang telah dimasukkan untuk risiko ini</p>

            <div style="display:flex; flex-direction:column; gap:12px;">
                @forelse ($topRisk->monitoringBulanan as $monitoring)
                    @php
                        $levelUrutan = (int) ($monitoring->level->urutan ?? 0);
                        $lvlStyle = match($levelUrutan) {
                            1 => 'background:#ecfdf5;color:#10b981;',
                            2 => 'background:#eff6ff;color:#3b82f6;',
                            3 => 'background:#fffbeb;color:#f59e0b;',
                            4 => 'background:#fff7ed;color:#f97316;',
                            5 => 'background:#fef2f2;color:#ef4444;',
                            default => 'background:#f1f5f9;color:#64748b;',
                        };
                        $statusStyle = ($monitoring->status === 'Aktif')
                            ? 'background:#ecfdf5;color:#10b981;'
                            : 'background:#fef2f2;color:#ef4444;';
                    @endphp

                    <div x-data="{ editOpen: false }"
                         style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">

                        {{-- Header Row --}}
                        <div style="padding:12px 20px; border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    {{-- 1. Bulan & Tahun --}}
                                    <span style="background:#1e293b; color:#fff; border-radius:20px; padding:4px 14px; font-size:13px; font-weight:700;">
                                        {{ $monthNames[(int) $monitoring->bulan] ?? $monitoring->bulan }} {{ $monitoring->tahun }}
                                    </span>

                                    {{-- 2. Inherent (Tepat di kanan Bulan) --}}
                                    <span style="background:#f8fafc; border:1px solid #e2e8f0; color:#475569; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        Inherent {{ $monitoring->inherent ?? '-' }}
                                    </span>

                                    {{-- 3. Level --}}
                                    <span style="{{ $lvlStyle }} border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        {{ $monitoring->level->nama_level ?? '-' }}
                                    </span>

                                    {{-- 4. Status --}}
                                    <span style="{{ $statusStyle }} border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">
                                        {{ $monitoring->status }}
                                    </span>
                                </div>

                                {{-- Tombol Edit & Hapus --}}
                                <div style="display:flex; gap:4px; align-items:center;">
                                    <button type="button" @click="editOpen = !editOpen"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('top-risk.monitoring.destroy', [$topRisk, $monitoring]) }}"
                                          onsubmit="return confirm('Hapus record monitoring ini?')" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg border border-rose-100 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Details Grid (Sekarang 5 Kolom: Nilai + 3 Progres + Efektivitas) --}}
                        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; padding:16px 20px;">
                            {{-- Kotak 1: NILAI --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Nilai</p>
                                <p style="font-size:16px; font-weight:700; color:#4f46e5; margin:0;">{{ $monitoring->nilai ?? '-' }}</p>
                            </div>

                            {{-- Kotak 2: Progres Belum --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Progres Belum</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $monitoring->progres_belum ?? 0 }}</p>
                            </div>

                            {{-- Kotak 3: Progres Proses --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Progres Proses</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $monitoring->progres_proses ?? 0 }}</p>
                            </div>

                            {{-- Kotak 4: Progres Sudah --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Progres Sudah</p>
                                <p style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $monitoring->progres_sudah ?? 0 }}</p>
                            </div>

                            {{-- Kotak 5: Efektivitas --}}
                            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; background:#fafbfc;">
                                <p style="font-size:10px; font-weight:600; color:#94a3b8; margin:0 0 4px; text-transform:uppercase;">Efektivitas</p>
                                <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">
                                    {{ $monitoring->aturanEfektivitas->hasil ?? 'Belum ada pembanding' }}
                                </p>
                            </div>
                        </div>

                        @if($monitoring->catatan)
                            <div style="padding:0 20px 12px 20px;">
                                <p style="font-size:12px; color:#64748b; margin:0; padding:8px 12px; background:#f8fafc; border-radius:8px; border:1px solid #f1f5f9;">
                                    Catatan: {{ $monitoring->catatan }}
                                </p>
                            </div>
                        @endif

                        {{-- Collapse Form Edit Inline --}}
                        <div x-show="editOpen" x-transition style="display:none; border-top:1px solid #f1f5f9; padding:16px 20px; background:#fafbfc;">
                            <form method="POST" action="{{ route('top-risk.monitoring.update', [$topRisk, $monitoring]) }}">
                                @csrf @method('PUT')
                                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:12px;">
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Bulan</label>
                                        <select name="bulan" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff;">
                                            @foreach ($monthNames as $monthNumber => $monthName)
                                                <option value="{{ $monthNumber }}" @selected((int) $monitoring->bulan === $monthNumber)>
                                                    {{ $monthName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Tahun</label>
                                        <input type="number" name="tahun" value="{{ $monitoring->tahun }}" min="2000"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Nilai (1-25)</label>
                                        <input type="number" name="nilai" value="{{ $monitoring->nilai }}" min="1" max="25"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff; font-weight:700;">
                                    </div>
                                </div>

                                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:12px;">
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Status</label>
                                        <select name="status" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; background:#fff;">
                                            <option value="Aktif" @selected($monitoring->status === 'Aktif')>Aktif</option>
                                            <option value="Tidak Aktif" @selected($monitoring->status === 'Tidak Aktif')>Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Belum</label>
                                        <input type="number" name="progres_belum" value="{{ $monitoring->progres_belum }}" min="0"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Proses</label>
                                        <input type="number" name="progres_proses" value="{{ $monitoring->progres_proses }}" min="0"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Progres Sudah</label>
                                        <input type="number" name="progres_sudah" value="{{ $monitoring->progres_sudah }}" min="0"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                    <div>
                                        <label style="font-size:12px; font-weight:600; color:#1e293b; display:block; margin-bottom:5px;">Catatan</label>
                                        <input type="text" name="catatan" value="{{ $monitoring->catatan }}"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; outline:none; box-sizing:border-box; background:#fff;">
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    <button type="button" @click="editOpen = false"
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
                                        Batal
                                    </button>
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
                        Belum ada riwayat monitoring bulanan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('topRiskMonitoring', (riskId, defaultInherent) => ({
                riskId: riskId,
                inherentDisplay: defaultInherent,
                levelDisplay: '-',
                loadingInherent: false,
                nilaiInput: '{{ old('nilai', '') }}',
                levelName: 'Otomatis',
                levelId: '',
                levelStyle: '',
                bulan: '{{ old('bulan', now()->month) }}',
                tahun: '{{ old('tahun', now()->year) }}',

                init() {
                    // Jalankan fetch pertama kali saat halaman di-load
                    this.fetchInherentPeriod();

                    // Watcher untuk update level realisasi otomatis saat input angka berubah
                    this.$watch('nilaiInput', (value) => {
                        this.updateLevel(value);
                    });
                },

                // Fungsi penarik data inherent dinamis via AJAX
                fetchInherentPeriod() {
                    if (!this.bulan || !this.tahun) return;

                    this.loadingInherent = true;

                    fetch(`/top-risk/${this.riskId}/inherent-period?bulan=${this.bulan}&tahun=${this.tahun}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.inherentDisplay = data.inherent ?? '-';
                        this.levelDisplay = data.level_name ?? '-';
                    })
                    .catch(() => {
                        this.inherentDisplay = defaultInherent;
                    })
                    .finally(() => {
                        this.loadingInherent = false;
                    });
                },

                updateLevel(value) {
                    const val = parseInt(value);
                    if (isNaN(val) || val < 1 || val > 25) {
                        this.levelName = 'Otomatis';
                        this.levelId = '';
                        this.levelStyle = '';
                        return;
                    }

                    let levelData = { name: '', id: '', color: '', bg: '' };

                    if (val >= 1 && val <= 5) {
                        levelData = { name: 'Low', id: 1, color: '#166534', bg: '#ecfdf5' };
                    } else if (val >= 6 && val <= 11) { // 6-11
                        levelData = { name: 'Low to Moderate', id: 2, color: '#1d4ed8', bg: '#eff6ff' };
                    } else if (val >= 12 && val <= 15) { // 12-15
                        levelData = { name: 'Moderate', id: 3, color: '#b45309', bg: '#fffbeb' };
                    } else if (val >= 16 && val <= 19) {
                        levelData = { name: 'Moderate to High', id: 4, color: '#c2410c', bg: '#fff7ed' };
                    } else if (val >= 20 && val <= 25) {
                        levelData = { name: 'High', id: 5, color: '#b91c1c', bg: '#fef2f2' };
                    }

                    this.levelName = levelData.name;
                    this.levelId = levelData.id;
                    this.levelStyle = `background:${levelData.bg}; color:${levelData.color}; padding:4px 12px; border-radius:6px; font-weight:700;`;
                }
            }));
        });
    </script>
</x-admin-layout>
