<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">Detail Monitoring Risiko Departemen</h1>
        <p class="hidden text-sm text-slate-500 sm:block">Manajemen parameter dan riwayat triwulan risiko.</p>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 font-semibold flex items-center gap-2">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 font-semibold flex flex-col gap-2">
                <span>{{ session('error') ?? 'Gagal menyimpan data.' }}</span>
                @if($errors->any())
                    <ul class="list-disc pl-5 font-normal">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Detail Lengkap</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('department-risk.index') }}" class="rounded-2xl border bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">Kembali</a>
                    <a href="{{ route('department-risk.edit', $risk->id_monitoring) }}" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Edit</a>
                </div>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div><span class="block text-xs font-semibold text-slate-400">Unit Kerja</span> <span class="block text-sm font-bold">{{ $risk->unitKerja->nama_unit ?? '-' }}</span></div>
                <div><span class="block text-xs font-semibold text-slate-400">Kategori</span> <span class="block text-sm font-bold">{{ $risk->kategoriRisiko->nama_kategori ?? '-' }}</span></div>
                <div><span class="block text-xs font-semibold text-slate-400">Type</span> <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium">{{ $risk->type }}</span></div>
                <div class="md:col-span-2 lg:col-span-3">
                    <span class="block text-xs font-semibold text-slate-400">Risk Event Detail</span>
                    <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">{{ $risk->risk_event_deta }}</div>
                </div>
            </div>
        </div>

        {{-- Mengambil data level untuk mapping ID ke Nama --}}
        @php
            $levelMap = \App\Models\LevelRisiko::all()->pluck('nama_level', 'id_level')->toJson();
        @endphp

        <form method="POST" action="{{ route('department-risk.update-period', $risk->id_monitoring) }}" x-data="smapRiskForm('{{ old('value', '') }}', '{{ old('inherent', '') }}', '{{ old('trend', 'Stabil') }}')">
            @csrf @method('PUT')
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-bold text-slate-900">+ Input Parameter Risiko</h2>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold">Triwulan</label>
                        <select name="quarter" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500">
                            <option value="TW1">TW1</option> <option value="TW2">TW2</option> <option value="TW3">TW3</option> <option value="TW4">TW4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Tahun</label>
                        <input type="number" name="year" value="{{ date('Y') }}" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Value (Residual)</label>
                        <input type="number" name="value" x-model="value" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Inherent</label>
                        <input type="number" name="inherent" x-model="inherent" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500" required>
                    </div>

                    {{-- Bagian Level Risiko dengan Mapping Nama --}}
                    <div x-data="{ levelMap: {{ $levelMap }} }">
                        <label class="block text-sm font-semibold text-slate-700">Level Risiko</label>
                        {{-- Input Hidden: Mengirim ID angka ke Controller --}}
                        <input type="hidden" name="calculated_level" :value="otomatisLevel">

                        {{-- Input Text: Menampilkan Nama Level berdasarkan ID --}}
                        <input type="text"
                               :value="levelMap[otomatisLevel] || '--'"
                               disabled
                               class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">Trend</label>
                        <input type="hidden" name="trend" :value="otomatisTrend">
                        <input type="text" :value="otomatisTrend" disabled class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm cursor-not-allowed">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Parameter</button>
                </div>
            </div>
        </form>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4">Riwayat Parameter Triwulan</h2>
            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase">
                        <tr><th class="px-4 py-3">Periode</th> <th class="px-4 py-3">Level</th> <th class="px-4 py-3">Value</th> <th class="px-4 py-3">Inherent</th> <th class="px-4 py-3">Trend</th> <th class="px-4 py-3">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($risk->periods as $period)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $period->pivot->quarter }} {{ $period->pivot->year }}</td>
                                <td class="px-4 py-3">{{ $period->nama_level ?? $period->level }}</td>
                                <td class="px-4 py-3 font-bold">{{ $period->pivot->value }}</td>
                                <td class="px-4 py-3">{{ $period->pivot->inherent }}</td>
                                <td class="px-4 py-3">{{ $period->pivot->trend }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('department-risk.destroy-period', [$risk->id_monitoring, $period->pivot->id]) }}" method="POST" onsubmit="return confirm('Hapus data periode ini?')">
                                        @csrf @method('DELETE') <button type="submit" class="text-rose-600 font-semibold hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/otomatisasi-logic.js') }}"></script>
</x-admin-layout>
