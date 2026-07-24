@php
    // Cukup 1 baris ini untuk mengunci default ke 'all' kalau variable dari controller kosong/null
    $selectedPeriode = $selectedPeriode ?? 'all';
@endphp

<div class="space-y-6">
    <!-- Filter Form -->
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('smap-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            <div class="lg:col-span-3">
                <label for="periode" class="block text-sm font-semibold text-slate-700">
                    Periode (Triwulan)
                </label>

                <select
                    id="periode"
                    name="periode"
                    class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <!-- Default 'all' untuk menampilkan semua triwulan -->
                    <option value="all" @selected((string)$selectedPeriode === 'all')>
                        Semua Triwulan
                    </option>

                    @foreach ([
                        1 => 'Triwulan I',
                        2 => 'Triwulan II',
                        3 => 'Triwulan III',
                        4 => 'Triwulan IV',
                    ] as $periodeNumber => $periodeName)
                        <option value="{{ $periodeNumber }}" @selected((string)$selectedPeriode === (string)$periodeNumber)>
                            {{ $periodeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="tahun" class="block text-sm font-semibold text-slate-700">
                    Tahun
                </label>

                <input
                    id="tahun"
                    type="number"
                    name="tahun"
                    value="{{ $selectedYear ?? date('Y') }}"
                    min="2000"
                    class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 lg:w-auto">
                    Tampilkan Dashboard
                </button>
            </div>
        </form>
    </div>

    <!-- Partials Clean Include PERSIS BENTUK ASLIMU -->
    @include('smap.partials._summary-cards')

    @include('smap.partials._list-departement')

    @include('smap.partials._list-level')

    @include('smap.partials._list-kategori')

    @include('smap.partials._list-trend')

    @include('smap.partials._chart-komposisi')

    @include('smap.partials._chart-pie-risiko')

    @include('smap.partials._chart-pie-penanganan')

    @include('smap.partials._chart-pie-efektif')
</div>
