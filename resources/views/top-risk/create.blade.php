<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('top-risk.index') }}"
               class="flex h-7 w-7 items-center justify-center rounded text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Tambah Top Risk</h1>
                <p class="text-xs text-slate-500">Tambahkan data risiko yang akan dipantau secara bulanan</p>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b; margin-bottom:16px;">
            <ul style="margin:0; padding-left:16px;">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('top-risk.store') }}">
        @csrf

        {{-- CARD UTAMA --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- BARIS ATAS: Kolom kiri (form) + Kolom kanan (textarea) --}}
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
                                        <option value="" disabled selected>Masukkan nama Kategori</option>
                                        @foreach ($kategoriRisiko as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" @selected((int) old('id_kategori') === (int) $kategori->id_kategori)>
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
                                <input type="date" name="tanggal_dibuat"
                                       value="{{ old('tanggal_dibuat', now()->toDateString()) }}"
                                       required
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
                                                   @checked(old('is_aktif', '1') === '1')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Aktif</span>
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:4px 0;">
                                            <input type="radio" name="is_aktif" value="0"
                                                   style="width:16px; height:16px; accent-color:#4F7EF0; cursor:pointer;"
                                                   @checked(old('is_aktif') === '0')>
                                            <span style="font-size:13px; color:#475569; font-weight:500;">Non-Aktif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>

                    {{-- KOLOM KANAN: Nama Peristiwa Risiko (Keterangan) --}}
                    <td style="padding:24px; vertical-align:top;">
                        <div style="display:flex; flex-direction:column; height:100%;">
                            <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Keterangan <span style="color:#ef4444;">*</span></label>
                            <textarea name="nama_peristiwa_risiko" required
                                      placeholder="Masukkan Keterangan tambahan"
                                      style="flex:1; width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px; font-size:13px; color:#475569; background:#fff; resize:none; outline:none; min-height:200px; font-family:inherit; line-height:1.6;">{{ old('nama_peristiwa_risiko') }}</textarea>
                            @error('nama_peristiwa_risiko') <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p> @enderror
                        </div>
                    </td>

                </tr>
            </table>

            {{-- DIVIDER --}}
            <div style="border-top:2px solid #f1f5f9;"></div>

            {{-- BARIS BAWAH: Unit Kerja Terkait --}}
            <div style="padding:24px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:4px;">Unit Kerja Terkait <span style="color:#ef4444;">*</span></label>
                <p style="font-size:12px; color:#94a3b8; margin-bottom:14px;">Pilih satu atau lebih unit kerja yang berkaitan dengan risiko ini.</p>

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
                                       @checked(in_array($unit->id_unit, old('unit_kerja', [])))>
                                <span style="font-size:13px; color:#475569; font-weight:500;">{{ $unit->nama_unit }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- TOMBOL AKSI --}}
        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:12px; padding:0 4px;">
            <a href="{{ route('top-risk.index') }}"
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
                Simpan
            </button>
        </div>

    </form>
</x-admin-layout>
