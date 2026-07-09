<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('department-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            <div class="lg:col-span-3">
                <label for="periode" class="block text-sm font-semibold text-slate-700">Periode (Triwulan)</label>
                <select id="periode" name="periode" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ([1 => 'Triwulan I', 2 => 'Triwulan II', 3 => 'Triwulan III', 4 => 'Triwulan IV'] as $periodeNumber => $periodeName)
                        <option value="{{ $periodeNumber }}" @selected((int) $selectedPeriode === $periodeNumber)>{{ $periodeName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="tahun" class="block text-sm font-semibold text-slate-700">Tahun</label>
                <input id="tahun" type="number" name="tahun" value="{{ $selectedYear }}" min="2000" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 lg:w-auto">
                    Tampilkan Dashboard
                </button>
            </div>
        </form>
    </div>

    @include('departemen.partial._summary-cards')

    @include('departemen.partial._departemen-chart')

    @include('departemen.partial._level-distribution')

    @include('departemen.partial._category-distribution')

    @include('departemen.partial._trend-risk')

    @include('departemen.partial._matrix-rekap')
</div>
