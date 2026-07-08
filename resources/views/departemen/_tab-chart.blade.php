<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('department-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            <div class="lg:col-span-3">
                <label for="unit_filter" class="block text-sm font-semibold text-slate-700">
                    Unit Kerja
                </label>

                <select
                    id="unit_filter"
                    name="unit_id"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id_unit }}" @selected((string) $unitId === (string) $unit->id_unit)>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="category_filter" class="block text-sm font-semibold text-slate-700">
                    Kategori Risiko
                </label>

                <select
                    id="category_filter"
                    name="category_id"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_kategori }}" @selected((string) $categoryId === (string) $category->id_kategori)>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="trend_filter" class="block text-sm font-semibold text-slate-700">
                    Trend
                </label>

                <select
                    id="trend_filter"
                    name="trend"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Trend</option>
                    <option value="Naik" @selected($trend === 'Naik')>Naik</option>
                    <option value="Turun" @selected($trend === 'Turun')>Turun</option>
                    <option value="Stabil" @selected($trend === 'Stabil')>Stabil</option>
                </select>
            </div>

            <div class="lg:col-span-3 lg:flex lg:justify-end">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 lg:w-auto">
                    Tampilkan Dashboard
                </button>
            </div>
        </form>
    </div>

    {{-- Dashboard dalam proses pembuatan --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-12 shadow-sm">
        <div class="flex flex-col items-center justify-center text-center">

            {{-- Icon --}}
            <div class="relative">
                <div class="absolute inset-0 animate-ping rounded-full bg-indigo-100 opacity-75"></div>
                <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-indigo-50">
                    <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
            </div>

            {{-- Text --}}
            <h3 class="mt-6 text-xl font-bold text-slate-900">
                Dashboard Sedang dalam Proses Pembuatan
            </h3>

            <div class="mt-3 max-w-md">
                <p class="text-sm text-slate-500">
                    Dashboard untuk Risk Department saat ini sedang dikembangkan.
                    Fitur ini akan segera hadir untuk memudahkan Anda memonitoring risiko departemen secara visual.
                </p>
            </div>

            {{-- Progress Bar --}}
            <div class="mt-6 w-full max-w-md">
                <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="absolute left-0 top-0 h-full w-1/3 rounded-full bg-indigo-600 animate-pulse"></div>
                </div>
                <p class="mt-2 text-xs text-slate-400">
                    Progress pengembangan: 30%
                </p>
            </div>

            {{-- Estimated Time --}}
            <div class="mt-6 flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm text-amber-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Estimasi selesai: Segera</span>
            </div>

            {{-- Back to Data Button --}}
            <div class="mt-8">
                <a
                    href="{{ route('department-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke Data Risk Department
                </a>
            </div>

        </div>
    </div>
</div>
