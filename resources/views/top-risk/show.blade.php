<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Detail Top Risk
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Detail risiko dan riwayat monitoring bulanan.
        </p>
    </x-slot>

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $topRisk->kategori->nama_kategori ?? '-' }}
                        </span>

                        @if ($topRisk->is_aktif)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <h2 class="max-w-4xl text-xl font-bold leading-8 text-slate-900">
                        {{ $topRisk->nama_peristiwa_risiko }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Tanggal dibuat:
                        <span class="font-semibold text-slate-700">
                            {{ optional($topRisk->tanggal_dibuat)->format('d M Y') ?? $topRisk->tanggal_dibuat }}
                        </span>
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($topRisk->unitKerja as $unit)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $unit->nama_unit }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('top-risk.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </a>

                    <a
                        href="{{ route('top-risk.edit', $topRisk) }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                        Edit Risiko
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Input Monitoring Bulanan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Nilai risiko diisi berdasarkan matrix risiko. Level risiko dan efektivitas akan dihitung otomatis oleh sistem.
                </p>
            </div>

            <form method="POST" action="{{ route('top-risk.monitoring.store', $topRisk) }}" class="grid gap-5 lg:grid-cols-12">
                @csrf

                <div class="lg:col-span-3">
                    <label for="bulan" class="block text-sm font-semibold text-slate-700">
                        Bulan
                    </label>

                    <select
                        id="bulan"
                        name="bulan"
                        required
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih bulan</option>
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) old('bulan', now()->month) === $monthNumber)>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>

                    @error('bulan')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-3">
                    <label for="tahun" class="block text-sm font-semibold text-slate-700">
                        Tahun
                    </label>

                    <input
                        id="tahun"
                        type="number"
                        name="tahun"
                        value="{{ old('tahun', now()->year) }}"
                        required
                        min="2000"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('tahun')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-3">
                    <label for="nilai" class="block text-sm font-semibold text-slate-700">
                        Nilai
                    </label>

                    <input
                        id="nilai"
                        type="number"
                        name="nilai"
                        value="{{ old('nilai') }}"
                        required
                        min="1"
                        max="25"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="1-25">

                    @error('nilai')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700">
                        Level Risiko
                    </label>

                    <div
                        id="level-preview"
                        class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                        Otomatis mengikuti nilai risiko
                    </div>

                    <p id="level-preview-help" class="mt-2 text-xs text-slate-500">
                        Masukkan nilai 1 sampai 25 untuk melihat level.
                    </p>
                </div>

                <div class="lg:col-span-3">
                    <label for="status" class="block text-sm font-semibold text-slate-700">
                        Status Monitoring
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option>
                        <option value="Tidak Aktif" @selected(old('status') === 'Tidak Aktif')>Tidak Aktif</option>
                    </select>

                    @error('status')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-3">
                    <label for="progres_belum" class="block text-sm font-semibold text-slate-700">
                        Progres Belum
                    </label>

                    <input
                        id="progres_belum"
                        type="number"
                        name="progres_belum"
                        value="{{ old('progres_belum', 0) }}"
                        min="0"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="lg:col-span-3">
                    <label for="progres_proses" class="block text-sm font-semibold text-slate-700">
                        Progres Proses
                    </label>

                    <input
                        id="progres_proses"
                        type="number"
                        name="progres_proses"
                        value="{{ old('progres_proses', 0) }}"
                        min="0"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="lg:col-span-3">
                    <label for="progres_sudah" class="block text-sm font-semibold text-slate-700">
                        Progres Sudah
                    </label>

                    <input
                        id="progres_sudah"
                        type="number"
                        name="progres_sudah"
                        value="{{ old('progres_sudah', 0) }}"
                        min="0"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="lg:col-span-12">
                    <label for="catatan" class="block text-sm font-semibold text-slate-700">
                        Catatan
                    </label>

                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="3"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Catatan monitoring bulanan">{{ old('catatan') }}</textarea>

                    @error('catatan')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-12">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                        Simpan Monitoring
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Riwayat Monitoring Bulanan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Data terbaru ditampilkan paling atas.
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($topRisk->monitoringBulanan as $monitoring)

                    @php
                        $levelUrutan = (int) ($monitoring->level->urutan ?? 0);

                        $levelBadgeClass = match ($levelUrutan) {
                            1 => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                            2 => 'bg-lime-100 text-lime-800 ring-1 ring-lime-200',
                            3 => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
                            4 => 'bg-orange-100 text-orange-800 ring-1 ring-orange-200',
                            5 => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                            default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
                        };
                    @endphp

                    <div class="rounded-3xl border border-slate-200 bg-white p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">
                                        {{ $monthNames[(int) $monitoring->bulan] ?? $monitoring->bulan }} {{ $monitoring->tahun }}
                                    </span>

                                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                        Nilai {{ $monitoring->nilai }}
                                    </span>

                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $levelBadgeClass }}">
                                        {{ $monitoring->level->nama_level ?? '-' }}
                                    </span>

                                    @if ($monitoring->status === 'Aktif')
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4 grid gap-3 text-sm sm:grid-cols-4">
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <div class="text-xs text-slate-500">Belum</div>
                                        <div class="mt-1 font-bold text-slate-900">{{ $monitoring->progres_belum }}</div>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <div class="text-xs text-slate-500">Proses</div>
                                        <div class="mt-1 font-bold text-slate-900">{{ $monitoring->progres_proses }}</div>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <div class="text-xs text-slate-500">Sudah</div>
                                        <div class="mt-1 font-bold text-slate-900">{{ $monitoring->progres_sudah }}</div>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <div class="text-xs text-slate-500">Efektivitas</div>
                                        <div class="mt-1 font-bold text-slate-900">
                                            {{ $monitoring->aturanEfektivitas->hasil ?? 'Belum ada pembanding' }}
                                        </div>
                                    </div>
                                </div>

                                @if ($monitoring->catatan)
                                    <p class="mt-4 text-sm leading-6 text-slate-600">
                                        {{ $monitoring->catatan }}
                                    </p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('top-risk.monitoring.destroy', [$topRisk, $monitoring]) }}" onsubmit="return confirm('Yakin ingin menghapus data monitoring ini?')">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                                    Hapus
                                </button>
                            </form>
                        </div>

                        <details class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-700">
                                Edit monitoring bulan ini
                            </summary>

                            <form method="POST" action="{{ route('top-risk.monitoring.update', [$topRisk, $monitoring]) }}" class="mt-5 grid gap-5 lg:grid-cols-12">
                                @csrf
                                @method('PUT')

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Bulan
                                    </label>

                                    <select
                                        name="bulan"
                                        required
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach ($monthNames as $monthNumber => $monthName)
                                            <option value="{{ $monthNumber }}" @selected((int) $monitoring->bulan === $monthNumber)>
                                                {{ $monthName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Tahun
                                    </label>

                                    <input
                                        type="number"
                                        name="tahun"
                                        value="{{ $monitoring->tahun }}"
                                        required
                                        min="2000"
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Nilai
                                    </label>

                                    <input
                                        type="number"
                                        name="nilai"
                                        value="{{ $monitoring->nilai }}"
                                        required
                                        min="1"
                                        max="25"
                                        class="js-edit-nilai mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Level Risiko
                                    </label>

                                    <div
                                        class="js-edit-level-preview mt-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                        {{ $monitoring->level->nama_level ?? 'Akan dihitung ulang' }}
                                    </div>

                                    <p class="js-edit-level-help mt-2 text-xs text-slate-500">
                                        Level akan otomatis mengikuti nilai risiko.
                                    </p>
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Status
                                    </label>

                                    <select
                                        name="status"
                                        required
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="Aktif" @selected($monitoring->status === 'Aktif')>Aktif</option>
                                        <option value="Tidak Aktif" @selected($monitoring->status === 'Tidak Aktif')>Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Progres Belum
                                    </label>

                                    <input
                                        type="number"
                                        name="progres_belum"
                                        value="{{ $monitoring->progres_belum }}"
                                        min="0"
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Progres Proses
                                    </label>

                                    <input
                                        type="number"
                                        name="progres_proses"
                                        value="{{ $monitoring->progres_proses }}"
                                        min="0"
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Progres Sudah
                                    </label>

                                    <input
                                        type="number"
                                        name="progres_sudah"
                                        value="{{ $monitoring->progres_sudah }}"
                                        min="0"
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="lg:col-span-12">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Catatan
                                    </label>

                                    <textarea
                                        name="catatan"
                                        rows="3"
                                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $monitoring->catatan }}</textarea>
                                </div>

                                <div class="lg:col-span-12">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">
                                        Simpan Perubahan Monitoring
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-white text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                            </svg>
                        </div>

                        <h3 class="mt-3 text-sm font-semibold text-slate-900">
                            Belum ada data monitoring
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tambahkan monitoring bulanan pertama untuk risiko ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const defaultClass = 'mt-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600';

            const levelClasses = {
                low: 'mt-2 rounded-2xl border border-emerald-300 bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-200',
                lowModerate: 'mt-2 rounded-2xl border border-lime-300 bg-lime-100 px-4 py-3 text-sm font-semibold text-lime-800 ring-1 ring-lime-200',
                moderate: 'mt-2 rounded-2xl border border-yellow-300 bg-yellow-100 px-4 py-3 text-sm font-semibold text-yellow-800 ring-1 ring-yellow-200',
                moderateHigh: 'mt-2 rounded-2xl border border-orange-300 bg-orange-100 px-4 py-3 text-sm font-semibold text-orange-800 ring-1 ring-orange-200',
                high: 'mt-2 rounded-2xl border border-red-300 bg-red-100 px-4 py-3 text-sm font-semibold text-red-800 ring-1 ring-red-200',
                invalid: 'mt-2 rounded-2xl border border-rose-300 bg-rose-100 px-4 py-3 text-sm font-semibold text-rose-800 ring-1 ring-rose-200',
            };

            function resolveLevelByNilai(nilai) {
                if (nilai >= 20 && nilai <= 25) {
                    return {
                        label: 'High',
                        className: levelClasses.high,
                        help: 'Nilai 20 - 25 termasuk level High.',
                    };
                }

                if (nilai >= 16 && nilai <= 19) {
                    return {
                        label: 'Moderate to High',
                        className: levelClasses.moderateHigh,
                        help: 'Nilai 16 - 19 termasuk level Moderate to High.',
                    };
                }

                if (nilai >= 11 && nilai <= 15) {
                    return {
                        label: 'Moderate',
                        className: levelClasses.moderate,
                        help: 'Nilai 11 - 15 termasuk level Moderate.',
                    };
                }

                if (nilai >= 6 && nilai <= 10) {
                    return {
                        label: 'Low to Moderate',
                        className: levelClasses.lowModerate,
                        help: 'Nilai 6 - 10 termasuk level Low to Moderate.',
                    };
                }

                if (nilai >= 1 && nilai <= 5) {
                    return {
                        label: 'Low',
                        className: levelClasses.low,
                        help: 'Nilai 1 - 5 termasuk level Low.',
                    };
                }

                return {
                    label: 'Nilai tidak valid',
                    className: levelClasses.invalid,
                    help: 'Nilai risiko harus berada pada rentang 1 sampai 25.',
                };
            }

            function bindLevelPreview(nilaiInput, levelPreview, levelHelp) {
                if (!nilaiInput || !levelPreview || !levelHelp) {
                    return;
                }

                function updateLevelPreview() {
                    const nilai = Number(nilaiInput.value);

                    if (!nilaiInput.value) {
                        levelPreview.className = defaultClass;
                        levelPreview.textContent = 'Otomatis mengikuti nilai risiko';
                        levelHelp.textContent = 'Masukkan nilai 1 sampai 25 untuk melihat level.';
                        return;
                    }

                    const level = resolveLevelByNilai(nilai);

                    levelPreview.className = level.className;
                    levelPreview.textContent = level.label;
                    levelHelp.textContent = level.help;
                }

                nilaiInput.addEventListener('input', updateLevelPreview);
                updateLevelPreview();
            }

            bindLevelPreview(
                document.getElementById('nilai'),
                document.getElementById('level-preview'),
                document.getElementById('level-preview-help')
            );

            document.querySelectorAll('form').forEach(function (form) {
                bindLevelPreview(
                    form.querySelector('.js-edit-nilai'),
                    form.querySelector('.js-edit-level-preview'),
                    form.querySelector('.js-edit-level-help')
                );
            });
        });
    </script>

</x-admin-layout>