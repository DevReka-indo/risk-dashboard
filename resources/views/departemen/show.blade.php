<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Monitoring Risiko</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Manajemen parameter dan riwayat triwulan risiko.</p>
    </x-slot>

    <div class="space-y-6">
        {{-- 1. NOTIFIKASI FLASH MESSAGES & ERROR VALIDASI --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 font-semibold shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 font-semibold shadow-sm flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-rose-600 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') ?? 'Gagal menyimpan parameter triwulan. Silakan periksa inputan Anda:' }}</span>
                </div>
                @if($errors->any())
                    <ul class="list-disc pl-7 text-xs text-rose-600 font-normal space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- 2. DETAIL LENGKAP RISIKO (dengan tombol kembali & edit) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('department-risk.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>
                    <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700">
                        Edit Risiko
                    </a>
                </div>
            </div>
            <p class="text-xs text-slate-400">Seluruh informasi data risiko ini.</p>

            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Unit Kerja</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $risk->unitKerja->nama_unit ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</span>
                    <span class="mt-1 block text-sm font-bold text-slate-800">{{ $risk->kategoriRisiko->nama_kategori ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</span>
                    <span class="mt-1 block text-sm font-medium text-slate-800">
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">{{ $risk->type }}</span>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</span>
                    <span class="mt-1 block text-sm font-medium">
                        @if($risk->status)
                            <span class="inline-flex items-center rounded-md bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-800">Non-Aktif</span>
                        @endif
                    </span>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Risk Event Detail</span>
                    <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700 border border-slate-100">
                        {{ $risk->risk_event_deta }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. FORM INPUT PARAMETER TRIWULAN --}}
        <form method="POST" action="{{ route('department-risk.update-period', $risk->id_monitoring) }}"
              x-data="smapRiskForm('{{ old('value', '') }}', '{{ old('inherent', '') }}', '{{ old('trend', 'Stabil') }}')">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-bold text-slate-900">+ Input Parameter Risiko Per Triwulan</h2>
                <p class="text-xs text-slate-400">Pilih target triwulan beserta tahun, lalu tentukan nilai-nilai parameternya.</p>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    {{-- Triwulan --}}
                    <div>
                        <label for="quarter" class="block text-sm font-semibold text-slate-700">Triwulan <span class="text-rose-500">*</span></label>
                        <select id="quarter" name="quarter" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="TW1" @selected(old('quarter') === 'TW1')>TW1</option>
                            <option value="TW2" @selected(old('quarter') === 'TW2')>TW2</option>
                            <option value="TW3" @selected(old('quarter') === 'TW3')>TW3</option>
                            <option value="TW4" @selected(old('quarter') === 'TW4')>TW4</option>
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div>
                        <label for="year" class="block text-sm font-semibold text-slate-700">Tahun <span class="text-rose-500">*</span></label>
                        <input type="number" id="year" name="year" value="{{ old('year', '2026') }}" min="2020" max="2099"
                               class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Value / Residual --}}
                    <div>
                        <label for="value" class="block text-sm font-semibold text-slate-700">Value (Residual) <span class="text-rose-500">*</span></label>
                        <input type="number" id="value" name="value" x-model="value" min="1" max="25" required placeholder="Masukkan nilai 1 - 25"
                               class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Inherent --}}
                    <div>
                        <label for="inherent" class="block text-sm font-semibold text-slate-700">Inherent <span class="text-rose-500">*</span></label>
                        <input type="number" id="inherent" name="inherent" x-model="inherent" min="1" max="25" required placeholder="Masukkan nilai inherent 1 - 25"
                               class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Level Risiko (Otomatis & Disabled) --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Level Risiko <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="id_level" :value="otomatisLevel">
                        <select :value="otomatisLevel" disabled
                                class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 shadow-sm cursor-not-allowed">
                            <option value="">Pilih Level</option>
                            @foreach ($levels as $lvl)
                                <option value="{{ $lvl->id_level }}">{{ $lvl->level ?? $lvl->nama_level }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Trend (Otomatis & Disabled) --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Trend <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="trend" :value="otomatisTrend">
                        <select :value="otomatisTrend" disabled
                                class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 shadow-sm cursor-not-allowed">
                            <option value="Stabil">Stabil</option>
                            <option value="Naik">Naik</option>
                            <option value="Turun">Turun</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 transition">
                        Simpan Parameter Triwulan
                    </button>
                </div>
            </div>
        </form>

        {{-- 4. TABEL RIWAYAT PARAMETER PERIODE (DENGAN TOMBOL HAPUS) --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">Riwayat Parameter Triwulan</h2>
            <p class="text-xs text-slate-400">Daftar riwayat parameter yang telah dimasukkan untuk risiko ini.</p>

            <div class="mt-4 overflow-hidden border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-700 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3">Level Risiko</th>
                            <th class="px-4 py-3 text-center">Value</th>
                            <th class="px-4 py-3 text-center">Inherent</th>
                            <th class="px-4 py-3">Trend</th>
                            <th class="px-4 py-3">Tanggal Input</th>
                            <th class="px-4 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($risk->periods as $period)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $period->pivot->quarter }} {{ $period->pivot->year }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $colorClass = match ($period->nama_level ?? $period->level) {
                                            'Low' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'Low to Moderate' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'Moderate' => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'Moderate to High' => 'bg-red-100 text-red-600 border-red-200',
                                            'High' => 'bg-rose-100 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $colorClass }}">
                                        {{ $period->level ?? $period->nama_level }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-800">
                                    {{ $period->pivot->value }}
                                </td>
                                <td class="px-4 py-3 text-center text-slate-500">
                                    {{ $period->pivot->inherent }}
                                </td>
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    @if($period->pivot->trend === 'Naik')
                                        <span class="text-rose-600">↑ Naik</span>
                                    @elseif($period->pivot->trend === 'Turun')
                                        <span class="text-emerald-600">↓ Turun</span>
                                    @else
                                        <span class="text-slate-500">→ Stabil</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($period->pivot->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @can('risk.delete')
                                        <form action="{{ route('department-risk.destroy-period', [$risk->id_monitoring, $period->pivot->id]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data parameter untuk periode {{ $period->pivot->quarter }} {{ $period->pivot->year }} ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-xl border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400">
                                    Belum ada data riwayat triwulan untuk risiko ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 5. MEMANGGIL LOGIC SCRIPT OTOMATISASI --}}
    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
</x-admin-layout>