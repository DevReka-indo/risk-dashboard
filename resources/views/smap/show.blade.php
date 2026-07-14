<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail & Monitoring Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Kelola input nilai berkala dan riwayat perkembangan risiko.</p>
    </x-slot>

    {{-- Pengiriman data master ke constructor Alpine JS --}}
    <div class="space-y-6" x-data="smapRiskForm({{ json_encode($historyData ?? []) }}, '{{ date('Y') }}', {{ (int)($risk->inherent ?? 0) }}, {{ (int)($risk->inherent_target ?? 0) }})">

        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- 1. HEADER PROFIL RISIKO --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $risk->unitKerja->nama_unit ?? '-' }}
                        </span>
                        @if ($risk->status)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Tidak Aktif</span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">{{ $risk->risk_event_deta }}</h2>
                    <p class="text-xs text-slate-500">Tanggal dibuat: <span class="font-medium text-slate-700">{{ $risk->created_at?->translatedFormat('d M Y') ?? '-' }}</span></p>
                    <div class="pt-1">
                        <span class="inline-flex rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                            Kategori: {{ $risk->kategoriRisiko->nama_kategori ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('smap-risk.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>
                    <a href="{{ route('smap-risk.edit', $risk->id_smap) }}" class="rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-700">
                        Edit Risiko
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. ALL-IN-ONE INPUT CARD (MURNI 1 CARD ELEGAN TANPA SUB-CARD LAGI DI DALAMNYA) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900">Input Monitoring & Target Kuartal</h3>
                <p class="mt-1 text-sm text-slate-500">Kelola kuartal aktif, nilai perkembangan terkini, beserta status penanganan dalam satu panel.</p>
            </div>

            <form method="POST" action="{{ route('smap-risk.store-monitoring', $risk->id_smap) }}" class="space-y-6">
                @csrf

                {{-- KELOMPOK ATAS: INFORMASI WAKTU & STATUS (3 KOLOM BERJEJER) --}}
                <div class="grid gap-5 grid-cols-1 sm:grid-cols-3">
                    <div>
                        <label for="quarter" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kuartal</label>
                        <select name="quarter" id="quarter" x-model="quarter" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="">-- Pilih Triwulan --</option>
                            <option value="TW1">Triwulan 1 (TW1)</option>
                            <option value="TW2">Triwulan 2 (TW2)</option>
                            <option value="TW3">Triwulan 3 (TW3)</option>
                            <option value="TW4">Triwulan 4 (TW4)</option>
                        </select>
                        @error('quarter')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="year" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tahun</label>
                        <input id="year" type="number" name="year" x-model="year" min="2020" max="2099" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        @error('year')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_monitoring" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status Monitoring</label>
                        <select id="status_monitoring" name="status_monitoring" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <hr class="border-slate-100 my-2">

                {{-- KELOMPOK TENGAH: GRID 3 KOLOM UTAMA (KIRI - TENGAH - KANAN) --}}
                <div class="grid gap-6 grid-cols-1 md:grid-cols-3">

                    {{-- 1. KIRI: INHERENT AREA --}}
                    <div class="space-y-4">
                        <div>
                            <label for="inherent" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Inherent Score</label>
                            <input id="inherent" type="number" name="inherent" :value="inherent" readonly class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 text-slate-600 px-4 py-3 text-sm font-bold cursor-not-allowed shadow-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Level Inherent (Otomatis)</label>
                            <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 select-none"
                                x-text="inherentLevelName">
                            </div>
                        </div>
                    </div>

                    {{-- 2. TENGAH: VALUE CURRENT AREA --}}
                    <div class="space-y-4">
                        <div>
                            <label for="value" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Value Current (1-25)</label>
                            <input id="value" type="number" name="value" x-model="value" min="1" max="25" required placeholder="Input skor saat ini..." class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                            @error('value')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Level Current (Otomatis)</label>
                            <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-bold text-slate-800 shadow-sm"
                                x-text="otomatisLevelName || 'Menunggu input...'">
                            </div>
                            <input type="hidden" name="calculated_level" :value="otomatisLevel">
                        </div>
                    </div>

                    {{-- 3. KANAN: TARGET AREA --}}
                    <div class="space-y-4">
                        <div>
                            <label for="inherent_target" class="block text-xs font-bold uppercase tracking-wider text-indigo-500">Inherent Target</label>
                            <input id="inherent_target" type="number" name="inherent_target" :value="targetValue" readonly class="mt-2 w-full rounded-2xl border-indigo-200 bg-slate-50 text-slate-700 px-4 py-3 text-sm font-bold cursor-not-allowed shadow-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-indigo-400">Level Target (Otomatis)</label>
                            <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-indigo-100 px-4 py-3 text-sm font-bold text-indigo-900 shadow-sm"
                                x-text="otomatisTargetLevelName || 'Menunggu input...'">
                            </div>
                            <input type="hidden" name="calculated_level_target" :value="otomatisTargetLevel">
                        </div>
                    </div>

                </div>

                <hr class="border-slate-100 my-2">

                {{-- KELOMPOK BAWAH: PROGRES & TREND --}}
                <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                    <div>
                        <label for="status_penanganan" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Progres Penanganan Risiko</label>
                        <select id="status_penanganan" name="status_penanganan" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="belum" {{ old('status_penanganan') == 'belum' ? 'selected' : '' }}>🔴 Belum Dimulai</option>
                            <option value="proses" {{ old('status_penanganan') == 'proses' ? 'selected' : '' }}>🟡 Sedang Berjalan</option>
                            <option value="selesai" {{ old('status_penanganan') == 'selesai' ? 'selected' : '' }}>🟢 Selesai</option>
                        </select>
                        @error('status_penanganan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Hasil Analisis Trend Perubahan</label>
                        <div class="mt-2 flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2.5 shadow-sm">
                            <div class="text-base font-black text-slate-800" x-text="otomatisTrend"></div>
                            <span class="text-xs text-slate-400 font-medium">(Dihitung otomatis berdasarkan perkembangan skor)</span>
                        </div>
                        <input type="hidden" name="calculated_trend" :value="otomatisTrend">
                    </div>
                </div>

                {{-- SUBMIT FOOTER --}}
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full sm:w-auto rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-700 transition-all duration-200">
                        Simpan Data & Target Kuartal
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. RIWAYAT MONITORING KUARTAL --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h3 class="text-base font-bold text-slate-900">Riwayat Monitoring Kuartal</h3>
                <p class="mt-1 text-sm text-slate-500">Data terbaru ditampilkan paling atas.</p>
            </div>

            <div class="space-y-4">
                @forelse ($risk->detailPeriode as $history)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/60 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-lg bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                                    {{ $history->period->period_name ?? '-' }}
                                </span>
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                    Current Level: {{ $history->levelRisiko->nama_level ?? '-' }}
                                </span>
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                    Target Level: {{ \App\Models\LevelRisiko::find($history->id_level_target)->nama_level ?? '-' }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('smap-risk.destroy-monitoring', ['id_period' => $history->id_detail]) }}" onsubmit="return confirm('Hapus log riwayat kuartal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                    Hapus
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 grid gap-4 grid-cols-2 md:grid-cols-5">
                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Inherent</div>
                                <div class="mt-1 text-sm font-bold text-slate-800">{{ $history->inherent ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Score current</div>
                                <div class="mt-1 text-sm font-bold text-slate-800">{{ $history->value ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-indigo-50/30 p-3 border border-indigo-100">
                                <div class="text-xs text-indigo-400 font-medium">Inherent Target</div>
                                <div class="mt-1 text-sm font-bold text-indigo-900">{{ $history->inherent_target ?? 0 }}</div>
                            </div>

                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Trend Terhitung</div>
                                <div class="mt-1 text-sm font-bold text-slate-800">
                                    @if(($history->trend ?? '') === 'Naik')
                                        <span class="text-rose-600">↑ Naik</span>
                                    @elseif(($history->trend ?? '') === 'Turun')
                                        <span class="text-emerald-600">↓ Turun</span>
                                    @else
                                        <span class="text-slate-500">→ Stabil</span>
                                    @endif
                                </div>
                            </div>

                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Penanganan</div>
                                <div class="mt-1 text-xs font-extrabold uppercase">
                                    @if(trim(strtolower($history->status_penanganan)) === 'selesai')
                                        <span class="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">🟢 Selesai</span>
                                    @elseif(trim(strtolower($history->status_penanganan)) === 'proses')
                                        <span class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md">🟡 Proses</span>
                                    @else
                                        <span class="text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded-md">🔴 Belum</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
                        Belum ada riwayat perkembangan kuartal untuk risiko ini. Silakan tambahkan data di atas.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Script pemanggilan dengan cache-buster --}}
    <script src="{{ asset('js/smap-logic.js') }}?v={{ time() }}"></script>
</x-admin-layout>
