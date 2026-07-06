<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('top-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            <div class="lg:col-span-3">
                <label for="bulan" class="block text-sm font-semibold text-slate-700">
                    Bulan Monitoring
                </label>

                <select
                    id="bulan"
                    name="bulan"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ([
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
                    ] as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}" @selected((int) $selectedMonth === $monthNumber)>
                            {{ $monthName }}
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
                    value="{{ $selectedYear }}"
                    min="2000"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="lg:col-span-6 lg:flex lg:justify-end">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 lg:w-auto">
                    Tampilkan Dashboard
                </button>
            </div>
        </form>
    </div>

    @include('top-risk.partials._summary-cards', [
        'summary' => $dashboardData['summary'],
        'period' => $dashboardData['period'],
    ])

    @include('top-risk.partials._heatmap-risk', [
        'heatmap' => $dashboardData['heatmap'],
    ])

    @include('top-risk.partials._level-distribution', [
        'items' => $dashboardData['level_distribution'],
    ])

    @include('top-risk.partials._trend-risk', [
        'items' => $dashboardData['trend_risk'],
        'period' => $dashboardData['period'],
    ])

    <div class="grid gap-6 xl:grid-cols-2">
        @include('top-risk.partials._category-distribution', [
            'items' => $dashboardData['category_distribution'],
        ])

        @include('top-risk.partials._status-distribution', [
            'items' => $dashboardData['status_distribution'],
        ])
    </div>
</div>
