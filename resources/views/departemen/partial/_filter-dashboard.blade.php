<div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
    <form method="GET" action="{{ route('department-risk.index') }}" class="grid gap-4 md:grid-cols-3 md:items-end">
        <input type="hidden" name="tab" value="dashboard">

        {{-- Filter 1: Tahun --}}
        <div>
            <label for="filter_tahun" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Tahun</label>
            <select id="filter_tahun" name="tahun" class="block w-full truncate rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                <option value="">Semua Tahun</option>
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter 2: Triwulan --}}
        <div>
            <label for="filter_triwulan" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Triwulan</label>
            <select id="filter_triwulan" name="triwulan" class="block w-full truncate rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                <option value="">Semua Triwulan</option>
                <option value="TW1" @selected($triwulan == 'TW1')>TW1</option>
                <option value="TW2" @selected($triwulan == 'TW2')>TW2</option>
                <option value="TW3" @selected($triwulan == 'TW3')>TW3</option>
                <option value="TW4" @selected($triwulan == 'TW4')>TW4</option>
            </select>
        </div>

        {{-- Filter & Reset Buttons --}}
        <div class="flex gap-2">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-indigo-500/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z" />
                </svg>
                Filter
            </button>

            <a href="{{ route('department-risk.index', ['tab' => 'dashboard']) }}"
               class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-rose-600 focus:ring-2 focus:ring-slate-200">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </a>
        </div>
    </form>
</div>
