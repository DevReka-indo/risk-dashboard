<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('top-risk.show', $topRisk) }}"
               class="flex h-7 w-7 items-center justify-center rounded text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Edit Top Risk</h1>
                <p class="text-xs text-slate-500">Perbarui data risiko dan unit kerja terkait</p>
            </div>
        </div>
    </x-slot>

    @php
        $selectedUnitKerja = old('unit_kerja', $topRisk->unitKerja->pluck('id_unit')->toArray());
    @endphp

    @if ($errors->any())
        <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b; margin-bottom:16px;">
            <ul style="margin:0; padding-left:16px;">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('top-risk.update', $topRisk) }}" x-data="topRiskEditForm">
        @csrf
        @method('PUT')

        {{-- CARD UTAMA --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- BARIS ATAS: Kolom kiri (form) + Kolom kanan (textarea + Nilai Inheren + Skala Target) --}}
            <table style="width:100%; border-collapse:collapse;">
                <tr>

                    {{-- KOLOM KIRI --}}
                    <td style="width:270px; padding:24px; border-right:1px solid #f1f5f9; vertical-align:top; background:#fafbfc;">
                        <div style="display:flex; flex-direction:column; gap:18px;">

                            {{-- Kategori Risiko --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Kategori Risiko <span style="color:#ef4444;">*</span></label>
                                <div style="position:relative;">
                                    <select name="id_kategori" required
                                            style="width:100%; appearance:none; border:1px solid #e2e8f0; border-radius:10px; padding:9px 36px 9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; cursor:pointer;">
                                        <option value="" disabled>Pilih Kategori</option>
                                        @foreach ($kategoriRisiko as $kategori)
                                            <option value="{{ $kategori->id_kategori }}"
                                                    @selected((int) old('id_kategori', $topRisk->id_kategori) === (int) $kategori->id_kategori)>
                                                {{ $kategori->nama_kategori }}
                                            </option>
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
                                <input type="date" name="tanggal_dibuat" required
                                       value="{{ old('tanggal_dibuat', optional($topRisk->tanggal_dibuat)->format('Y-m-d') ?? $topRisk->tanggal_dibuat) }}"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box;">
                                @error('tanggal_dibuat') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                            </div>

                            {{-- Status Risiko --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Status Risiko</label>
                                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; background:#fff;">
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="is_aktif" value="1"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('is_aktif', $topRisk->is_aktif ? '1' : '0') === '1')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Aktif</span>
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="is_aktif" value="0"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('is_aktif', $topRisk->is_aktif ? '1' : '0') === '0')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Non-Aktif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>

                    {{-- KOLOM KANAN: Nama Peristiwa Risiko + Nilai Inheren + Skala Target --}}
                    <td style="padding:24px; vertical-align:top;">
                        <div style="display:flex; flex-direction:column; height:100%;">

                            {{-- Textarea Nama Peristiwa Risiko --}}
                            <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Nama Peristiwa Risiko <span style="color:#ef4444;">*</span></label>
                            <textarea name="nama_peristiwa_risiko" required
                                      placeholder="Masukkan deskripsi peristiwa risiko..."
                                      style="flex:1; width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px; font-size:13px; color:#475569; background:#fff; resize:none; outline:none; min-height:120px; font-family:inherit; line-height:1.6;">{{ old('nama_peristiwa_risiko', $topRisk->nama_peristiwa_risiko) }}</textarea>
                            @error('nama_peristiwa_risiko') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror

                            {{-- Divider tipis --}}
                            <div style="border-top:1px solid #f1f5f9; margin:14px 0;"></div>

                            {{-- Nilai Inheren Awal --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Nilai Inheren Awal <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                                    {{-- Input Nilai --}}
                                    <div style="position:relative;">
                                        <input type="number" name="inherent" x-model="inherent" min="1" max="25" required
                                               placeholder="1 - 25"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                        @error('inherent') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Level Inheren (Auto) --}}
                                    <div>
                                        <div style="border:2px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#1e293b; background:#f8fafc; text-align:center; height:38px; display:flex; align-items:center; justify-content:center; font-weight:600; box-sizing:border-box;">
                                            <span x-text="levelName || 'Otomatis'" :style="levelStyle"></span>
                                        </div>
                                        <input type="hidden" name="id_level" :value="levelId">
                                    </div>
                                </div>
                            </div>

                            {{-- Divider tipis --}}
                            <div style="border-top:1px solid #f1f5f9; margin:14px 0;"></div>

                            {{-- Skala Target (TW1 - TW4) --}}
                            <div>
                                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                                    Skala Target <span style="color:#ef4444;">*</span>
                                </label>
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:10px;">
                                    {{-- TW1 --}}
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">TW 1</label>
                                        <input type="number" name="target_tw1" min="1" max="25" required
                                               placeholder="1-25"
                                               value="{{ old('target_tw1', $topRisk->target_tw1) }}"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                        @error('target_tw1') <p style="margin-top:4px; font-size:10px; color:#ef4444;">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- TW2 --}}
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">TW 2</label>
                                        <input type="number" name="target_tw2" min="1" max="25" required
                                               placeholder="1-25"
                                               value="{{ old('target_tw2', $topRisk->target_tw2) }}"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                        @error('target_tw2') <p style="margin-top:4px; font-size:10px; color:#ef4444;">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- TW3 --}}
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">TW 3</label>
                                        <input type="number" name="target_tw3" min="1" max="25" required
                                               placeholder="1-25"
                                               value="{{ old('target_tw3', $topRisk->target_tw3) }}"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                        @error('target_tw3') <p style="margin-top:4px; font-size:10px; color:#ef4444;">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- TW4 --}}
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">TW 4</label>
                                        <input type="number" name="target_tw4" min="1" max="25" required
                                               placeholder="1-25"
                                               value="{{ old('target_tw4', $topRisk->target_tw4) }}"
                                               style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; height:38px;">
                                        @error('target_tw4') <p style="margin-top:4px; font-size:10px; color:#ef4444;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>

                </tr>
            </table>

            {{-- DIVIDER --}}
            <div style="border-top:2px solid #f1f5f9;"></div>

            {{-- BARIS BAWAH: Unit Kerja Terkait --}}
            <div style="padding:24px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:4px;">Unit Kerja Terkait <span style="color:#ef4444;">*</span></label>
                <p style="font-size:12px; color:#94a3b8; margin-bottom:14px;">Perbarui unit kerja yang berkaitan dengan risiko ini.</p>

                @error('unit_kerja') <p style="margin-bottom:10px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror

                @if ($unitKerja->isEmpty())
                    <p style="font-size:13px; color:#94a3b8; text-align:center; padding:24px 0;">Belum ada unit kerja.
                        <a href="{{ route('unit-kerja.create') }}" style="color:#4F7EF0; font-weight:600;">Tambah →</a>
                    </p>
                @else
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px;">
                        @foreach ($unitKerja as $unit)
                            <label style="display:flex; align-items:center; gap:10px; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; cursor:pointer; background:#fff; transition:border-color 0.2s;"
                                   onmouseover="this.style.borderColor='#4F7EF0'; this.style.background='#f0f5ff';"
                                   onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#fff';">
                                <input type="checkbox" name="unit_kerja[]" value="{{ $unit->id_unit }}"
                                       style="width:15px; height:15px; accent-color:#4F7EF0; cursor:pointer; flex-shrink:0;"
                                       @checked(in_array($unit->id_unit, $selectedUnitKerja))>
                                <span style="font-size:13px; color:#475569; font-weight:500;">{{ $unit->nama_unit }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- TOMBOL AKSI --}}
        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:12px; padding:0 4px;">
            <a href="{{ route('top-risk.show', $topRisk) }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
            Batal
            </a>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700">
            Simpan
            </button>
        </div>

    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('topRiskEditForm', () => ({
                inherent: '{{ old('inherent', $topRisk->inherent) }}',
                levelName: '',
                levelId: '',
                levelStyle: '',

                init() {
                    this.updateLevel();
                    this.$watch('inherent', () => this.updateLevel());
                },

                updateLevel() {
                    const val = parseInt(this.inherent);
                    if (isNaN(val) || val < 1 || val > 25) {
                        this.levelName = 'Otomatis';
                        this.levelId = '';
                        this.levelStyle = 'color:#94a3b8; font-weight:500;';
                        return;
                    }

                    let levelData = { name: '', id: '', color: '', bg: '' };

                    if (val >= 1 && val <= 5) {
                        levelData = { name: 'Low', id: 1, color: '#166534', bg: '#ecfdf5' };
                    } else if (val >= 6 && val <= 11) {
                        levelData = { name: 'Low to Moderate', id: 2, color: '#1d4ed8', bg: '#eff6ff' };
                    } else if (val >= 12 && val <= 15) {
                        levelData = { name: 'Moderate', id: 3, color: '#b45309', bg: '#fffbeb' };
                    } else if (val >= 16 && val <= 19) {
                        levelData = { name: 'Moderate to High', id: 4, color: '#c2410c', bg: '#fff7ed' };
                    } else if (val >= 20 && val <= 25) {
                        levelData = { name: 'High', id: 5, color: '#b91c1c', bg: '#fef2f2' };
                    }

                    this.levelName = levelData.name;
                    this.levelId = levelData.id;
                    this.levelStyle = `background:${levelData.bg}; color:${levelData.color}; padding:4px 12px; border-radius:6px; font-weight:700; width:100%; text-align:center;`;
                }
            }));
        });
    </script>
</x-admin-layout>
