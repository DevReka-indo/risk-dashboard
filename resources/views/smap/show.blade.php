<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail & Monitoring Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Kelola input nilai berkala dan riwayat perkembangan risiko.</p>
    </x-slot>

    {{-- x-data diisi parsing json saengga otomatisasi kuartal lan sajarah data saged diwaca Alpine --}}
    <div class="space-y-6" x-data="smapRiskForm({{ json_encode($historyData ?? []) }}, '{{ date('Y') }}')">

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

        {{-- 2. CARD INPUT MONITORING KUARTAL --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900">Input Monitoring & Target Kuartal</h3>
                <p class="mt-1 text-sm text-slate-500">Isi data kuartal berjalan beserta target risiko yang ingin dicapai secara berkala.</p>
            </div>

            <form method="POST" action="{{ route('smap-risk.store-monitoring', $risk->id_smap) }}" class="space-y-6">
                @csrf

                {{-- KELOMPOK 1: INFORMASI WAKTU & STATUS --}}
                <div class="grid gap-5 sm:grid-cols-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <label for="quarter" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Kuartal</label>
                        <select id="quarter" name="quarter" x-model="quarter" x-on:change="checkInherent()" required class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="">Pilih Kuartal</option>
                            {{-- 🔥 UBAH VALUE DI BAWAH INI MENJADI TW1, TW2, TW3, TW4 --}}
                            <option value="TW1">Q1 (Kuartal 1)</option>
                            <option value="TW2">Q2 (Kuartal 2)</option>
                            <option value="TW3">Q3 (Kuartal 3)</option>
                            <option value="TW4">Q4 (Kuartal 4)</option>
                        </select>
                        @error('quarter')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Tahun</label>
                        <input id="year" type="number" name="year" x-model="year" x-on:input="checkInherent()" min="2020" max="2099" required class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="status_monitoring" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status Monitoring</label>
                        <select id="status_monitoring" name="status_monitoring" required class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                {{-- DUA BLOK BERJEJER: CURRENT VS TARGET --}}
                <div class="grid gap-6 md:grid-cols-2">

                    {{-- KELOMPOK 2: KONDISI SEKARANG (CURRENT) --}}
                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-5 shadow-inner">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-slate-500"></span>
                            <h4 class="text-sm font-bold text-slate-700">Kondisi Berjalan (Current)</h4>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="value" class="block text-xs font-medium text-slate-600">Value (Score 1-25)</label>
                                <input id="value" type="number" name="value" x-model="value" min="1" max="25" required placeholder="0" class="mt-1.5 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                            </div>

                            <div>
                                <label for="inherent" class="block text-xs font-medium text-slate-600">Inherent Score</label>
                                <input id="inherent" type="number" name="inherent" x-model="inherent" :readonly="inherentReadOnly" required placeholder="0" class="mt-1.5 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-sm shadow-sm transition-colors" :class="inherentReadOnly ? 'bg-slate-100 text-slate-500' : 'bg-white text-slate-800'">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-400">Level Risiko Current (Otomatis)</label>
                            <div class="mt-1.5 w-full rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-bold text-slate-800 shadow-sm"
                                 x-text="{
                                    1: 'Low',
                                    2: 'Low Moderate',
                                    3: 'Moderate',
                                    4: 'Moderate to High',
                                    5: 'High'
                                 }[otomatisLevel] || 'Menunggu input...'">
                            </div>
                            <input type="hidden" name="calculated_level" :value="otomatisLevel">
                        </div>
                    </div>

                    {{-- KELOMPOK 3: TARGET PENURUNAN RISIKO --}}
                    <div class="space-y-4 rounded-2xl border border-indigo-100 bg-indigo-50/20 p-5 shadow-inner">
                        <div class="flex items-center gap-2 border-b border-indigo-50 pb-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-indigo-600"></span>
                            <h4 class="text-sm font-bold text-indigo-900">Target Risiko Selanjutnya</h4>
                        </div>

                        <div>
                            <label for="inherent_target" class="block text-xs font-medium text-indigo-700">Inherent Target (Score 1-25)</label>
                            <input id="inherent_target" type="number" name="inherent_target" x-model="targetValue" min="1" max="25" required placeholder="Masukkan skor target" class="mt-1.5 w-full rounded-xl border-indigo-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            @error('inherent_target')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-indigo-400">Level Risiko Target (Otomatis)</label>
                            <div class="mt-1.5 w-full rounded-xl bg-white border border-indigo-100 px-4 py-2 text-sm font-bold text-indigo-900 shadow-sm"
                                 x-text="{
                                    1: 'Low',
                                    2: 'Low Moderate',
                                    3: 'Moderate',
                                    4: 'Moderate to High',
                                    5: 'High'
                                 }[otomatisTargetLevel] || 'Menunggu input target...'">
                            </div>
                            <input type="hidden" name="calculated_level_target" :value="otomatisTargetLevel">
                        </div>
                    </div>

                </div>

                {{-- KELOMPOK 4: HASIL ANALISIS TREN --}}
                <div class="rounded-2xl border border-dashed border-slate-200 p-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Hasil Analisis Trend Perubahan</label>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="text-xl font-black text-slate-800" x-text="otomatisTrend"></div>
                        <span class="text-xs text-slate-400 font-medium">(Dihitung otomatis berdasarkan nilai perkembangan Value saat ini)</span>
                    </div>
                    <input type="hidden" name="calculated_trend" :value="otomatisTrend">
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="w-full sm:w-auto rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200">
                        Simpan Data & Target Kuartal
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. CARD RIWAYAT MONITORING KUARTAL --}}
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
                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                    Nilai: {{ $history->value ?? 0 }}
                                </span>
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                    Current: {{ $history->levelRisiko->nama_level ?? '-' }}
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

                        <div class="mt-4 grid gap-4 grid-cols-2 md:grid-cols-4">
                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Inherent Current</div>
                                <div class="mt-1 text-sm font-bold text-slate-800">{{ $history->inherent ?? 0 }}</div>
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
                                <div class="text-xs text-slate-400 font-medium">Waktu Submit</div>
                                <div class="mt-1 text-sm font-bold text-slate-600">{{ $history->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
                        Belum ada riwayat perkembangan kuartal untuk risiko ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Pemanggilan file logic utama sing wis disinkronisasi --}}
    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
</x-admin-layout>
