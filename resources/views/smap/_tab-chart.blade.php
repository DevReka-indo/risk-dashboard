<div class="space-y-6">
    {{-- Filter Dashboard --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('smap-risk.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <input type="hidden" name="tab" value="dashboard">

            <div class="lg:col-span-3">
                <label for="unit_filter" class="block text-sm font-semibold text-slate-700">
                    Unit Kerja
                </label>
                <select
                    id="unit_filter"
                    name="unit_id"
                    onchange="this.form.submit()"
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
                    onchange="this.form.submit()"
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
                    onchange="this.form.submit()"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Trend</option>
                    <option value="Naik" @selected($trend === 'Naik')>Naik</option>
                    <option value="Turun" @selected($trend === 'Turun')>Turun</option>
                    <option value="Stabil" @selected($trend === 'Stabil')>Stabil</option>
                </select>
            </div>

            <div class="lg:col-span-3 lg:flex lg:justify-end">
                <a
                    href="{{ route('smap-risk.index', array_merge(request()->except('page'), ['tab' => 'data'])) }}"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 lg:w-auto">
                    Kembali ke Data
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Risiko --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Total Risiko
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $smapRisks->total() }}
                    </p>
                </div>
                <div class="rounded-2xl bg-indigo-50 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Seluruh data risiko SMAP terdaftar
            </p>
        </div>

        {{-- Risiko Aktif --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Risiko Aktif
                    </p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">
                        {{ $smapRisks->where('status', 1)->count() }}
                    </p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-3">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Risiko dengan status aktif
            </p>
        </div>

        {{-- Rata-rata Nilai --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Rata-rata Nilai
                    </p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">
                        {{ number_format($smapRisks->avg('value') ?? 0, 1) }}
                    </p>
                </div>
                <div class="rounded-2xl bg-amber-50 p-3">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Rata-rata nilai risiko SMAP
            </p>
        </div>

        {{-- Trend --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Trend Risiko
                    </p>
                    @php
                        $trend = 'Stabil';
                        $trendClass = 'text-slate-600 bg-slate-100';
                        if ($smapRisks->avg('value') > $smapRisks->avg('inherent') ?? 0) {
                            $trend = 'Naik';
                            $trendClass = 'text-rose-600 bg-rose-50';
                        } elseif ($smapRisks->avg('value') < $smapRisks->avg('inherent') ?? 0) {
                            $trend = 'Turun';
                            $trendClass = 'text-emerald-600 bg-emerald-50';
                        }
                    @endphp
                    <div class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $trendClass }}">
                        {{ $trend }}
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-3">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Perubahan dari periode sebelumnya
            </p>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Grafik Distribusi per Unit Kerja --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900">Distribusi per Unit Kerja</h3>
                <p class="text-xs text-slate-400">Jumlah risiko berdasarkan unit kerja</p>
            </div>
            <div class="relative w-full" style="height: 250px;">
                <canvas id="unitChart"></canvas>
            </div>
        </div>

        {{-- Grafik Distribusi per Kategori --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900">Distribusi per Kategori</h3>
                <p class="text-xs text-slate-400">Jumlah risiko berdasarkan kategori</p>
            </div>
            <div class="relative w-full" style="height: 250px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Grafik Trend Nilai Risiko --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h3 class="text-base font-bold text-slate-900">Trend Nilai Risiko</h3>
            <p class="text-xs text-slate-400">Perubahan nilai risiko dari waktu ke waktu</p>
        </div>
        <div class="relative w-full" style="height: 300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

{{-- Script Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data dari Controller
        const unitLabels = {!! json_encode($unitLabels ?? []) !!};
        const unitData = {!! json_encode($unitData ?? []) !!};
        const categoryLabels = {!! json_encode($categoryLabels ?? []) !!};
        const categoryData = {!! json_encode($categoryData ?? []) !!};
        const trendLabels = {!! json_encode($trendLabels ?? []) !!};
        const trendData = {!! json_encode($trendData ?? []) !!};

        // Warna-warna untuk chart
        const colors = [
            'rgba(79, 70, 229, 0.85)', // Indigo
            'rgba(139, 92, 246, 0.85)', // Purple
            'rgba(236, 72, 153, 0.85)', // Pink
            'rgba(251, 146, 60, 0.85)', // Orange
            'rgba(16, 185, 129, 0.85)', // Emerald
            'rgba(59, 130, 246, 0.85)', // Blue
            'rgba(239, 68, 68, 0.85)', // Red
            'rgba(168, 85, 247, 0.85)', // Violet
        ];

        // Chart 1: Distribusi per Unit Kerja (Bar)
        if (document.getElementById('unitChart')) {
            const ctx1 = document.getElementById('unitChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: unitLabels,
                    datasets: [{
                        label: 'Jumlah Risiko',
                        data: unitData,
                        backgroundColor: colors.slice(0, unitLabels.length),
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // Chart 2: Distribusi per Kategori (Doughnut)
        if (document.getElementById('categoryChart')) {
            const ctx2 = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: colors.slice(0, categoryLabels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Chart 3: Trend Nilai Risiko (Line)
        if (document.getElementById('trendChart')) {
            const ctx3 = document.getElementById('trendChart').getContext('2d');
            new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Nilai Risiko',
                        data: trendData,
                        borderColor: 'rgba(79, 70, 229, 1)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
    });
</script>