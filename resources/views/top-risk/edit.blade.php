<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Edit Top Risk
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Perbarui data risiko dan unit kerja terkait.
        </p>
    </x-slot>

    @php
        $selectedUnitKerja = old('unit_kerja', $topRisk->unitKerja->pluck('id_unit')->toArray());
    @endphp

    <form method="POST" action="{{ route('top-risk.update', $topRisk) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label for="nama_peristiwa_risiko" class="block text-sm font-semibold text-slate-700">
                        Nama Peristiwa Risiko
                    </label>

                    <textarea
                        id="nama_peristiwa_risiko"
                        name="nama_peristiwa_risiko"
                        rows="3"
                        required
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('nama_peristiwa_risiko', $topRisk->nama_peristiwa_risiko) }}</textarea>

                    @error('nama_peristiwa_risiko')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_kategori" class="block text-sm font-semibold text-slate-700">
                        Kategori Risiko
                    </label>

                    <select
                        id="id_kategori"
                        name="id_kategori"
                        required
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategoriRisiko as $kategori)
                            <option value="{{ $kategori->id_kategori }}" @selected((int) old('id_kategori', $topRisk->id_kategori) === (int) $kategori->id_kategori)>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_kategori')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_dibuat" class="block text-sm font-semibold text-slate-700">
                        Tanggal Dibuat
                    </label>

                    <input
                        id="tanggal_dibuat"
                        type="date"
                        name="tanggal_dibuat"
                        value="{{ old('tanggal_dibuat', optional($topRisk->tanggal_dibuat)->format('Y-m-d') ?? $topRisk->tanggal_dibuat) }}"
                        required
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('tanggal_dibuat')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-2">
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <input
                            id="is_aktif"
                            type="checkbox"
                            name="is_aktif"
                            value="1"
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('is_aktif', $topRisk->is_aktif))>

                        <div>
                            <label for="is_aktif" class="text-sm font-semibold text-slate-900">
                                Risiko Aktif
                            </label>
                            <p class="mt-1 text-sm text-slate-500">
                                Jika tidak dicentang, risiko akan disimpan sebagai tidak aktif.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Unit Kerja Terkait
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Perbarui unit kerja yang berkaitan dengan risiko ini.
                </p>
            </div>

            @error('unit_kerja')
                <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($unitKerja as $unit)
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40">
                        <input
                            type="checkbox"
                            name="unit_kerja[]"
                            value="{{ $unit->id_unit }}"
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(in_array($unit->id_unit, $selectedUnitKerja))>

                        <span>
                            <span class="block text-sm font-semibold text-slate-900">
                                {{ $unit->nama_unit }}
                            </span>

                            @if ($unit->keterangan)
                                <span class="mt-1 block text-xs text-slate-500">
                                    {{ $unit->keterangan }}
                                </span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a
                href="{{ route('top-risk.show', $topRisk) }}"
                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-admin-layout>
