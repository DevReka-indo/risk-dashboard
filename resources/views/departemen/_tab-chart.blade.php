<div class="space-y-6">
    {{-- 1. Bagian Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('department-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            {{-- Filter Unit Kerja --}}
            <div class="lg:col-span-5">
                <label for="unit_filter" class="block text-sm font-semibold text-slate-700">Unit Kerja</label>
                <select id="unit_filter" name="unit_id" onchange="this.form.submit()" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id_unit }}" @selected((string) $unitId === (string) $unit->id_unit)>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori Risiko --}}
            <div class="lg:col-span-5">
                <label for="category_filter" class="block text-sm font-semibold text-slate-700">Kategori Risiko</label>
                <select id="category_filter" name="category_id" onchange="this.form.submit()" class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories ?? [] as $category)
                        <option value="{{ $category->id }}" @selected((string) ($categoryId ?? '') === (string) $category->id)>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Reset --}}
            <div class="lg:col-span-2">
                <a href="{{ route('department-risk.index', ['tab' => 'dashboard']) }}"
                   class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- 2. Kartu Ringkasan --}}
    @include('departemen.partial._summary-cards', ['summary' => $summary, 'period' => $period])

    {{-- 3. GRAFIK DISTRIBUSI DEPARTEMEN --}}
    @include('departemen._departemen-chart', ['chartLabels' => $chartLabels, 'chartData' => $chartData])

    {{-- 4. Distribusi Visual Lainnya --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Heatmap di Kiri --}}
        <div>
            @include('departemen.partial._heatmap-risk', ['heatmap' => $heatmapData])
        </div>

        {{-- Baris Kanan untuk Distribusi Level, Kategori & Status --}}
        <div class="space-y-6">
            @include('departemen.partial._level-distribution', ['items' => $levelDistribution])
            @include('departemen.partial._category-distribution', ['items' => $categoryDistribution])
            @include('departemen.partial._status-distribution', ['items' => $statusDistribution])
        </div>
    </div>
</div>
