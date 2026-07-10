<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Monitoring Risiko Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Manajemen parameter, target, penanganan, dan riwayat triwulan risiko.</p>
    </x-slot>

    {{-- Data untuk Alpine.js --}}
    @php
        $historyData = [];
        foreach($risk->periods as $p) {
            $historyData[$p->pivot->year][$p->pivot->quarter] = $p->pivot->value;
        }
    @endphp

    <div class="space-y-6">
        {{-- Alerts Success / Error Session --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 font-semibold flex items-center gap-2">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 font-semibold flex items-center gap-2">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Jaring Pengaman --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-bold mb-2">Gagal menyimpan data karena alasan berikut:</p>
                <ul class="list-disc pl-5 space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Detail Informasi Utama --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <a href="{{ route('department-risk.index') }}" class="rounded-2xl border bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">Kembali</a>
            </div>
            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div><span class="block text-xs font-semibold text-slate-400">Unit Kerja</span> <span class="block text-sm font-bold">{{ $risk->unitKerja->nama_unit ?? '-' }}</span></div>
                <div><span class="block text-xs font-semibold text-slate-400">Kategori</span> <span class="block text-sm font-bold">{{ $risk->kategoriRisiko->nama_kategori ?? '-' }}</span></div>
                <div><span class="block text-xs font-semibold text-slate-400">Type</span> <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium">{{ $risk->type }}</span></div>
            </div>
        </div>

        {{-- Form Monitoring (Alpine.js Component) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            x-data="smapRiskForm(@js($historyData), '{{ old('year', date('Y')) }}')">

            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900">Input Monitoring Kuartal</h3>
                <p class="mt-1 text-sm text-slate-500">Nilai inherent otomatis terisi berdasarkan kuartal sebelumnya.</p>
            </div>

            <form method="POST" action="{{ route('department-risk.update-period', $risk->id_monitoring) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Baris Input --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Kuartal & Tahun --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Kuartal</label>
                        <select name="quarter" x-model="quarter" @change="checkInherent()" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="TW1">TW1</option>
                            <option value="TW2">TW2</option>
                            <option value="TW3">TW3</option>
                            <option value="TW4">TW4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Tahun</label>
                        <input type="number" name="year" x-model="year" @change="checkInherent()" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Skor Inherent (Awal)</label>
                        <input type="number" name="inherent" x-model="inherent" :readonly="inherentReadOnly" :class="inherentReadOnly ? 'bg-slate-50 text-slate-500' : 'bg-white'" required class="mt-2 w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Status Penanganan</label>
                        <select name="penanganan" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Belum">Belum</option>
                            <option value="Proses">Proses</option>
                            <option value="Sudah">Sudah</option>
                        </select>
                    </div>

                    {{-- Skor & Level --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Skor Current (1-25)</label>
                        <input type="number" name="value" x-model="value" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Level Current</label>
                        <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600"
                            x-text="{1: 'Low', 2: 'Low Moderate', 3: 'Moderate', 4: 'Moderate to High', 5: 'High'}[otomatisLevel] || '-'">
                        </div>
                        <input type="hidden" name="calculated_level" :value="otomatisLevel">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Skor Target (1-25)</label>
                        <input type="number" name="target" x-model="targetValue" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Level Target</label>
                        <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600"
                            x-text="{1: 'Low', 2: 'Low to Moderate', 3: 'Moderate', 4: 'Moderate to High', 5: 'High'}[otomatisTargetLevel] || '-'">
                        </div>
                        <input type="hidden" name="calculated_target_level" :value="otomatisTargetLevel">
                    </div>

                    {{-- Baris Status & Trend --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">Status Monitoring</label>
                        <select name="status_monitoring" required class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">Trend Perubahan</label>
                        <div class="mt-2 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600" x-text="otomatisTrend || '-'"></div>
                        <input type="hidden" name="calculated_trend" :value="otomatisTrend">
                    </div>
                </div>

                {{-- Footer Form --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="reset" class="px-6 py-3 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Reset</button>
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-12 py-3 text-sm font-bold text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition shadow-sm">
                        Simpan Monitoring
                    </button>
                </div>
            </form>
        </div>

        {{-- Riwayat Parameter Triwulan --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">Riwayat Parameter Triwulan</h2>
            </div>
            <div class="space-y-4">
                @forelse ($risk->periods as $period)
                    @php
                        $level = strtolower($period->nama_level ?? $period->level ?? '');
                        $levelBadgeClass = match ($level) {
                            'high' => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                            'moderate to high' => 'bg-orange-100 text-orange-800 ring-1 ring-orange-200',
                            'moderate' => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
                            'low to moderate' => 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
                            'low' => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                            default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
                        };

                        $targetId = $period->pivot->target_id_level;
                        $targetName = match((int) $targetId) {
                            1 => 'Low', 2 => 'Low Moderate', 3 => 'Moderate', 4 => 'Moderate to High', 5 => 'High',
                            default => 'N/A'
                        };
                        $targetBadgeClass = match((int) $targetId) {
                            5 => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                            4 => 'bg-orange-100 text-orange-800 ring-1 ring-orange-200',
                            3 => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
                            2 => 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
                            1 => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                            default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
                        };
                        $penanganan = $period->pivot->penanganan ?? 'Belum';
                        $penangananBadge = match($penanganan) {
                            'Sudah' => 'bg-emerald-100 text-emerald-700',
                            'Proses' => 'bg-blue-100 text-blue-700',
                            default => 'bg-slate-100 text-slate-600'
                        };
                    @endphp

                    <div class="rounded-3xl border border-slate-200 bg-white p-5 hover:border-indigo-100 transition shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex rounded-full bg-slate-900 px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                                    {{ $period->pivot->quarter }} {{ $period->pivot->year }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $penangananBadge }}">
                                    {{ $penanganan }}
                                </span>
                            </div>
                            <form action="{{ route('department-risk.destroy-period', [$risk->id_monitoring, $period->pivot->id]) }}" method="POST" onsubmit="return confirm('Hapus data periode ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 border border-rose-200 rounded-full px-4 py-1.5 text-xs font-bold hover:bg-rose-50 transition">Hapus</button>
                            </form>
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <div class="text-xs font-semibold text-slate-500">Inherent Score</div>
                                <div class="mt-2 text-lg font-bold text-slate-900">{{ $period->pivot->inherent ?? '-' }}</div>
                            </div>
                            <div class="rounded-2xl border border-indigo-50 bg-indigo-50/30 p-4">
                                <div class="text-xs font-semibold text-slate-500">Current Score</div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-lg font-bold text-slate-900">{{ $period->pivot->value ?? '-' }}</span>
                                    <span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $levelBadgeClass }}">
                                        {{ $period->nama_level ?? $period->level ?? 'No Level' }}
                                    </span>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-emerald-50 bg-emerald-50/30 p-4">
                                <div class="text-xs font-semibold text-slate-500">Target Score</div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-lg font-bold text-slate-900">{{ $period->pivot->target_value ?? '-' }}</span>
                                    <span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $targetBadgeClass }}">
                                        {{ $targetName }}
                                    </span>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <div class="text-xs font-semibold text-slate-500">Trend</div>
                                <div class="mt-2 font-bold text-slate-900">{{ $period->pivot->trend ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-slate-400 text-sm">
                        Belum ada data riwayat triwulan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="{{ asset('js/otomatisasi-logic.js') }}?v={{ time() }}"></script>
</x-admin-layout>
