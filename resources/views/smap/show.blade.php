<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail & Monitoring Risk SMAP</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Kelola input nilai berkala dan riwayat perkembangan risiko.</p>
    </x-slot>

    <div class="space-y-6" x-data="smapRiskForm('{{ old('value', 0) }}', '{{ old('inherent', 0) }}')">

        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- 1. HEADER PROFIL RISIKO (Blok Atas) --}}
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
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Tidak Atif</span>
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

        {{-- 2. CARD INPUT MONITORING KUARTAL (Blok Tengah) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h3 class="text-base font-bold text-slate-900">Input Monitoring Kuartal</h3>
                <p class="mt-1 text-sm text-slate-500">Nilai level dan trend risiko dihitung otomatis secara real-time dari data score yang dimasukkan.</p>
            </div>

            <form method="POST" action="{{ route('smap-risk.store-monitoring', $risk->id_smap) }}" class="space-y-6">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Pilihan Kuartal --}}
                    <div>
                        <label for="quarter" class="block text-sm font-medium text-slate-700">Kuartal</label>
                        <select id="quarter" name="quarter" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                            <option value="">Pilih Kuartal</option>
                            <option value="Q1" @selected(old('quarter') == 'Q1')>Q1 (Kuartal 1)</option>
                            <option value="Q2" @selected(old('quarter') == 'Q2')>Q2 (Kuartal 2)</option>
                            <option value="Q3" @selected(old('quarter') == 'Q3')>Q3 (Kuartal 3)</option>
                            <option value="Q4" @selected(old('quarter') == 'Q4')>Q4 (Kuartal 4)</option>
                        </select>
                    </div>

                    @error('quarter')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Input Tahun --}}
                    <div>
                        <label for="year" class="block text-sm font-medium text-slate-700">Tahun</label>
                        <input id="year" type="number" name="year" value="{{ old('year', date('Y')) }}" min="2020" max="2099" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                    </div>

                    {{-- Input Value --}}
                    <div>
                        <label for="value" class="block text-sm font-medium text-slate-700">Value (Score 1-25)</label>
                        <input id="value" type="number" name="value" x-model="value" min="1" max="25" required placeholder="0" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                    </div>

                    {{-- Input Inherent --}}
                    <div>
                        <label for="inherent" class="block text-sm font-medium text-slate-700">Inherent</label>
                        <input id="inherent" type="number" name="inherent" x-model="inherent" required placeholder="0" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                    </div>

                    {{-- Status Monitoring --}}
                    <div>
                        <label for="status_monitoring" class="block text-sm font-medium text-slate-700">Status Monitoring</label>
                        <select id="status_monitoring" name="status_monitoring" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    {{-- Tampilan Level Terotomatisasi --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-400">Level Risiko (Otomatis)</label>
                        <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm"
                             x-text="{
                                1: 'Low',
                                2: 'Low Moderate',
                                3: 'Moderate',
                                4: 'Moderate to High',
                                5: 'High'
                             }[otomatisLevel] || 'Pilih level'">
                        </div>
                        <input type="hidden" name="calculated_level" :value="otomatisLevel">
                    </div>

                    {{-- Tampilan Trend Terotomatisasi --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-400">Trend Perubahan (Otomatis)</label>
                        <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm" x-text="otomatisTrend"></div>
                        <input type="hidden" name="calculated_trend" :value="otomatisTrend">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 transition">
                        Simpan Monitoring
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. CARD RIWAYAT MONITORING KUARTAL (Blok Bawah) --}}
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
                                    {{ $history->levelRisiko->nama_level ?? '-' }}
                                </span>
                            </div>

                            {{-- Form Destroy yang sudah disempurnakan --}}
                            <form method="POST" action="{{ route('smap-risk.destroy-monitoring', ['id_period' => $history->id_detail]) }}" onsubmit="return confirm('Hapus log riwayat kuartal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                    Hapus
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 grid gap-4 grid-cols-2 md:grid-cols-3">
                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Inherent Score</div>
                                <div class="mt-1 text-sm font-bold text-slate-800">{{ $history->inherent ?? 0 }}</div>
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
                            <div class="col-span-2 md:col-span-1 rounded-xl bg-white p-3 border border-slate-100">
                                <div class="text-xs text-slate-400 font-medium">Waktu Submit</div>
                                <div class="mt-1 text-sm font-bold text-slate-600">{{ $history->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
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

    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
</x-admin-layout>
