<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Pusat pantauan metrik risiko, tindakan penanganan, dan prioritas perusahaan.
        </p>
    </x-slot>

    <style>
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulseGlow { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        .kpi-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        .kpi-card:hover {
            transform: translateY(-4px);
        }
        .kpi-card .card-icon {
            transition: all 0.3s ease;
        }
        .kpi-card:hover .card-icon {
            transform: scale(1.1) rotate(-5deg);
        }
        
        .card-active-rose { 
            border-color: #f43f5e !important; 
            background: linear-gradient(135deg, #fff1f2, #ffffff) !important;
            box-shadow: 0 4px 20px rgba(244, 63, 94, 0.15) !important;
        }
        .card-active-indigo { 
            border-color: #6366f1 !important; 
            background: linear-gradient(135deg, #eef2ff, #ffffff) !important;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15) !important;
        }
        .card-active-emerald { 
            border-color: #10b981 !important; 
            background: linear-gradient(135deg, #ecfdf5, #ffffff) !important;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15) !important;
        }
        
        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Tab navigation dalam content */
        .tab-nav-btn {
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
        }
        .tab-nav-btn.active {
            color: #1e293b;
            font-weight: 600;
        }
        .tab-nav-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #f43f5e;
            border-radius: 9999px;
        }
        .tab-nav-btn:not(.active) {
            color: #94a3b8;
        }
        .tab-nav-btn:not(.active):hover {
            color: #64748b;
        }

        /* Table border style */
        .table-border-custom {
            border-collapse: collapse;
        }
        .table-border-custom th,
        .table-border-custom td {
            border: 1px solid #1e293b;
        }
    </style>

    <div class="space-y-6 pb-10">
{{-- ================================================================= --}}
{{-- 1. KPI CARDS --}}
{{-- ================================================================= --}}
<div class="grid grid-cols-1 gap-6 md:grid-cols-3 animate-fade-in-up">

    {{-- Card 1: Top Risk --}}
    <div id="card-top_risk" 
         onclick="switchTab('top_risk')" 
         class="kpi-card card-active-rose group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-rose-200 bg-gradient-to-br from-rose-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-rose-300 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        Prioritas
                    </span>
                </div>
                <p class="text-sm font-semibold text-slate-600">Top Risk</p>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ number_format($stats['high_risks']) }}
                </h2>
                <p class="text-xs text-slate-400">Risiko dengan prioritas tertinggi</p>
            </div>
            
            {{-- Icon Top Risk (Menggunakan warna Rose solid) --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white shadow-md shadow-rose-500/30 transition-transform duration-300 group-hover:scale-110">
                <svg class="h-6 w-6 stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-rose-600 border-t border-rose-100/60 pt-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ number_format($stats['high_risks']) }} risiko kritis butuh penanganan</span>
        </div>
    </div>
{{-- Card 2: SMAP --}}
<div id="card-smap" 
     onclick="switchTab('smap')" 
     class="kpi-card group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-purple-200 bg-gradient-to-br from-purple-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-300 hover:shadow-xl">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-500"></span>
                    Kepatuhan
                </span>
            </div>
            <p class="text-sm font-semibold text-slate-600">SMAP</p>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ number_format($stats['total_smap']) }}
            </h2>
            <p class="text-xs text-slate-400">Sistem Manajemen Anti Penyuapan</p>
        </div>

        {{-- Icon SMAP: Dikunci dengan Inline CSS biar ga dirusak JS/CSS lain --}}
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl shadow-md transition-transform duration-300 group-hover:scale-110" 
             style="background-color: #9333ea !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(147, 51, 234, 0.39) !important;">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0112 2.714z" />
            </svg>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-purple-600 border-t border-purple-100/60 pt-3">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ number_format($stats['total_smap']) }} risiko aktif terpantau</span>
    </div>
</div>

    {{-- Card 3: Departemen --}}
    <div id="card-dep" 
         onclick="switchTab('dep')" 
         class="kpi-card group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        Operasional
                    </span>
                </div>
                <p class="text-sm font-semibold text-slate-600">Departemen</p>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ number_format($stats['total_dep']) }}
                </h2>
                <p class="text-xs text-slate-400">Monitoring risiko per departemen</p>
            </div>

            {{-- Icon Departemen (Menggunakan warna Blue solid) --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md shadow-blue-500/30 transition-transform duration-300 group-hover:scale-110">
                <svg class="h-6 w-6 stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3a1.5 1.5 0 011.5-1.5h3a1.5 1.5 0 011.5 1.5v3" />
                </svg>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-blue-600 border-t border-blue-100/60 pt-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M3.75 4.5h16.5m-16.5 3.75h16.5" />
            </svg>
            <span>{{ number_format($stats['total_dep']) }} unit risiko terkelola</span>
        </div>
    </div>

</div>

        {{-- ================================================================= --}}
        {{-- AREA KONTEN 1: TOP RISK (dengan Tab di dalamnya) --}}
        {{-- ================================================================= --}}
        <div id="content-top_risk" class="tab-content space-y-6 animate-fade-in-up delay-100">
            
            {{-- Tab Navigation --}}
            <div class="flex items-center justify-between border-b-2 border-slate-200 pb-0">
                <div class="flex gap-6">
                    <button onclick="switchTopRiskTab('analisis')" 
                            id="tab-analisis" 
                            class="tab-nav-btn active pb-3 text-sm font-medium text-slate-800">
                        Analisis & Daftar
                    </button>
                    <button onclick="switchTopRiskTab('heatmap')" 
                            id="tab-heatmap" 
                            class="tab-nav-btn pb-3 text-sm font-medium text-slate-400 hover:text-slate-600">
                        Heatmap Risiko
                    </button>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-full border border-rose-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    Live
                </span>
            </div>

            {{-- Tab Content: Analisis --}}
            <div id="toprisk-analisis" class="toprisk-tab-content space-y-6">
                {{-- Grafik --}}
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Distribusi Level Risiko</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Persentase tingkat bahaya dari seluruh risiko teridentifikasi.</p>
                        <div class="relative h-64 w-full flex justify-center">
                            <canvas id="levelChart" data-level-distribution="{{ json_encode($levelDistribution) }}"></canvas>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41L11 3.83a2 2 0 00-2.83 0L3.83 8.17a2 2 0 000 2.83L13.41 20.6a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.84z" />
                                    <circle cx="7.5" cy="7.5" r="1.3" fill="currentColor" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Kategori Risiko Terbanyak</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Konsentrasi isu berdasarkan kelompok risiko.</p>
                        <div class="relative h-64 w-full">
                            <canvas id="categoryChart" data-categories="{{ json_encode($riskCategories) }}"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-rose-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="border-b border-rose-100 bg-gradient-to-r from-rose-50/50 to-white p-5">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                <h3 class="text-sm font-bold text-rose-800">Top 5 Risiko Kritis</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Memerlukan perhatian dan mitigasi segera.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($topHighRisks as $risk)
                                    <tr class="hover:bg-rose-50/30 transition">
                                        <td class="p-4">
                                            <p class="font-bold text-slate-800 line-clamp-1">{{ $risk->risk_event_deta }}</p>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m4.5-6h1.5m-1.5 3h1.5m-1.5 3h1.5" />
                                                </svg>
                                                {{ $risk->unitKerja->nama_unit ?? 'Unit Umum' }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-right">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 2 5-7" />
                                                </svg>
                                                Skala: {{ $risk->value ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="p-6 text-center text-slate-400">Belum ada risiko kategori kritis.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="border-b border-slate-100 p-5">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <h3 class="text-sm font-bold text-slate-800">Pembaruan Penanganan Terakhir</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Log aktivitas monitoring terbaru.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($recentUpdates as $update)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-4">
                                            <p class="font-semibold text-slate-800 line-clamp-1">{{ $update->risk_event_deta }}</p>
                                            <p class="text-xs text-indigo-600 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $update->updated_at->diffForHumans() }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-right">
                                            @if($update->penanganan == 'Sudah')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @elseif($update->penanganan == 'Proses')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Diproses
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="p-6 text-center text-slate-400">Belum ada aktivitas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Content: Heatmap --}}
            <div id="toprisk-heatmap" class="toprisk-tab-content hidden space-y-6">
                @include('top-risk.partials._heatmap-risk')
            </div>

        </div>

      {{-- ================================================================= --}}
{{-- AREA KONTEN 2: DEPARTEMEN --}}
{{-- ================================================================= --}}
<div id="content-dep" class="tab-content hidden space-y-6 animate-fade-in-up">
    {{-- Header Section --}}
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Analisis Risiko Departemen</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring risiko operasional per departemen/unit kerja</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-100/70 px-3 py-1.5 rounded-full border border-blue-200">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Terpantau
        </span>
    </div>

    {{-- Statistik Ringkas (Dipertegas & Konsisten) --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        
        {{-- Card 1: Total Risiko --}}
        <div class="group relative overflow-hidden rounded-2xl border-2 border-slate-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-slate-400 hover:shadow-xl">
            {{-- Accent Top Bar Slate/Dark --}}
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #475569 !important;"></div>

            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Risiko</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($stats['total_dep']) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Semua unit kerja</p>
                </div>
                {{-- Icon Solid Slate --}}
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
                     style="background-color: #475569 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(71, 85, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H4.125A1.125 1.125 0 0 0 3 3.375v17.25c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125v-6.375Z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-slate-600 border-t-2 border-slate-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #475569 !important;"></span>
                <span>Akumulasi Risiko Operasional</span>
            </div>
        </div>

        {{-- Card 2: Aktif --}}
        <div class="group relative overflow-hidden rounded-2xl border-2 border-blue-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl">
            {{-- Accent Top Bar Biru --}}
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #2563eb !important;"></div>

            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Aktif</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($stats['total_dep']) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Dalam proses monitoring</p>
                </div>
                {{-- Icon Solid Biru --}}
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
                     style="background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-blue-700 border-t-2 border-blue-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #2563eb !important;"></span>
                <span>Sedang Ditangani</span>
            </div>
        </div>

        {{-- Card 3: Selesai --}}
        <div class="group relative overflow-hidden rounded-2xl border-2 border-emerald-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-xl">
            {{-- Accent Top Bar Hijau --}}
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #059669 !important;"></div>

            <div class="flex items-start justify-between pt-1">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Selesai</p>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($departemenData['progresData'][2] ?? 0) }}
                    </p>
                    <p class="text-xs font-medium text-slate-500 pt-1">Mitigasi berhasil</p>
                </div>
                {{-- Icon Solid Hijau --}}
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
                     style="background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-700 border-t-2 border-emerald-100 pt-3">
                <span class="w-2 h-2 rounded-full" style="background-color: #059669 !important;"></span>
                <span>Telah Termitigasi</span>
            </div>
        </div>

    </div>

    {{-- Grafik Risiko per Departemen (Bar Chart) --}}
    @include('departemen.partial._departemen-chart')

    {{-- Pie Chart Inherent, Current, Target --}}
    @include('departemen.partial._chart-pie')

    {{-- Pie Chart Jenis Risiko --}}
    @include('departemen.partial._chart-jenis')

    {{-- Pie Chart Efektifitas --}}
    @include('departemen.partial._chart-efektif')

    {{-- Progres Penanganan Risiko --}}
    @include('departemen.partial._chart-progres')
</div>
      {{-- ================================================================= --}}
{{-- AREA KONTEN 3: SMAP --}}
{{-- ================================================================= --}}
<div id="content-smap" class="tab-content hidden space-y-6 animate-fade-in-up">
    {{-- Header Section --}}
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Sistem Manajemen Anti Penyuapan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring kepatuhan dan risiko anti penyuapan</p>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-700 bg-purple-100/70 px-3 py-1.5 rounded-full border border-purple-200">
            <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
            Kepatuhan
        </span>
    </div>

    {{-- Statistik SMAP (Dipertegas & Kontras Tinggi) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    {{-- Card 1: Total Risiko SMAP (Fixed & Dipertegas) --}}
<div class="group relative overflow-hidden rounded-2xl border-2 border-purple-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-purple-500 hover:shadow-xl">
    {{-- Accent Top Bar Ungu --}}
    <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: #9333ea !important;"></div>

    <div class="flex items-start justify-between pt-1">
        <div class="space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-purple-900">Total Risiko SMAP</p>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ number_format($stats['total_smap']) }}
            </p>
            <p class="text-xs font-medium text-slate-500 pt-1">Seluruh risiko terpantau</p>
        </div>
        {{-- Icon Solid Ungu --}}
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
             style="background-color: #9333ea !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(147, 51, 234, 0.4) !important;">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-purple-700 border-t-2 border-purple-100 pt-3">
        <span class="w-2 h-2 rounded-full" style="background-color: #9333ea !important;"></span>
        <span>Total Resiko</span>
    </div>
</div>

    {{-- Card 2: Titik Rawan --}}
    <div class="group relative overflow-hidden rounded-2xl border-2 border-amber-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-amber-500 hover:shadow-xl">
        {{-- Accent Top Bar --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-amber-500"></div>

        <div class="flex items-start justify-between pt-1">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-900">Titik Rawan</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight">3</span>
                    <span class="text-sm font-bold text-amber-700">Area</span>
                </div>
                <p class="text-xs font-medium text-slate-500 pt-1">Dalam pemantauan ketat</p>
            </div>
            {{-- Icon Solid Amber/Orange --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
                 style="background-color: #d97706 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(217, 119, 6, 0.4) !important;">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5ZM10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.375h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z" />
                </svg>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-amber-700 border-t-2 border-amber-100 pt-3">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Butuh pengawasan ekstra</span>
        </div>
    </div>

    {{-- Card 3: Kepatuhan ISO 37001 --}}
    <div class="group relative overflow-hidden rounded-2xl border-2 border-emerald-300 bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-xl">
        {{-- Accent Top Bar --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-600"></div>

        <div class="flex items-start justify-between pt-1">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Kepatuhan ISO 37001</p>
                <p class="text-3xl font-extrabold text-slate-900 tracking-tight">100%</p>
                <p class="text-xs font-medium text-slate-500 pt-1">Seluruh modul telah dinilai</p>
            </div>
            {{-- Icon Solid Emerald/Hijau --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" 
                 style="background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.4) !important;">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-700 border-t-2 border-emerald-100 pt-3">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            <span>100% Terverifikasi</span>
        </div>
    </div>

</div>

            {{-- Visualisasi Pie Chart SMAP (Inherent, Current, Target) --}}
            @if(isset($smapPieData) && !empty($smapPieData))
                @include('smap.partials._chart-pie-risiko')
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-bold text-slate-800">Belum Ada Data SMAP</h3>
                    <p class="mt-2 text-sm text-slate-500">Data visualisasi peta risiko SMAP akan ditampilkan di sini setelah tersedia.</p>
                </div>
            @endif

            {{-- Komposisi Risk Owner --}}
            @if(isset($smapKomposisiData) && !empty($smapKomposisiData))
                @include('smap.partials._chart-komposisi')
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-bold text-slate-800">Belum Ada Data Komposisi</h3>
                    <p class="mt-2 text-sm text-slate-500">Data komposisi risk owner akan ditampilkan di sini setelah tersedia.</p>
                </div>
            @endif

            {{-- Efektivitas Mitigasi Risiko --}}
            @if(isset($smapPieData) && !empty($smapPieData) && isset($smapPieData['efektif']))
                @include('smap.partials._chart-pie-efektif')
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-bold text-slate-800">Belum Ada Data Efektivitas</h3>
                    <p class="mt-2 text-sm text-slate-500">Data efektivitas mitigasi risiko akan ditampilkan di sini setelah tersedia.</p>
                </div>
            @endif

            {{-- Progres Penanganan Risiko SMAP --}}
            @if(isset($smapPieData) && !empty($smapPieData) && isset($smapPieData['progres']))
                @include('smap.partials._chart-pie-penanganan')
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-bold text-slate-800">Belum Ada Data Progres</h3>
                    <p class="mt-2 text-sm text-slate-500">Data progres penanganan risiko akan ditampilkan di sini setelah tersedia.</p>
                </div>
            @endif

            {{-- Daftar Risiko SMAP --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0 1 12 2.714Z" />
                    </svg>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Risiko Anti Penyuapan</h3>
                </div>
                
                @if(isset($smapRisks) && !empty($smapRisks))
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
    
                                    <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-slate-600">Risiko</th>
                                    <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-slate-600">Level</th>
                                    <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-slate-600 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($smapRisks as $risk)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $risk->risk_event_deta ?? 'Risiko SMAP' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold
                                            @if($risk->levelRisiko->nama_level == 'High' || $risk->levelRisiko->nama_level == 'Tinggi') bg-rose-100 text-rose-700
                                            @elseif($risk->levelRisiko->nama_level == 'Moderate to High') bg-orange-100 text-orange-700
                                            @elseif($risk->levelRisiko->nama_level == 'Moderate') bg-amber-100 text-amber-700
                                            @elseif($risk->levelRisiko->nama_level == 'Low to Moderate') bg-lime-100 text-lime-700
                                            @else bg-emerald-100 text-emerald-700
                                            @endif
                                        ">
                                            {{ $risk->levelRisiko->nama_level ?? 'Tidak Terdefinisi' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($risk->penanganan == 'Sudah')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                                Selesai
                                            </span>
                                        @elseif($risk->penanganan == 'Proses')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Diproses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="p-6 text-center text-slate-400">Belum ada data risiko SMAP.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-xl">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-slate-800">Modul SMAP Terkendali</h3>
                        <p class="mt-1 text-xs text-slate-500">Tidak ditemukan insiden pelanggaran atau penyuapan pada periode ini.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- AI Chat Widget --}}
<div id="ai-chat-widget" data-csrf="{{ csrf_token() }}" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
    
    {{-- Window Chat --}}
    <div id="ai-chat-window" class="hidden mb-4 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col h-[28rem] transition-all duration-300 transform origin-bottom-right">
        <div class="p-4 text-white flex justify-between items-center" style="background: linear-gradient(to right, #4f46e5, #4338ca) !important;">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-white">Risk AI Assistant</h3>
                    <p class="text-[10px] text-indigo-200">Online</p>
                </div>
            </div>
            <button onclick="toggleAiChat()" class="text-indigo-200 hover:text-white transition rounded-full hover:bg-white/10 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 text-sm scroll-smooth">
            <div class="flex items-start gap-2">
                <div class="bg-indigo-100 text-indigo-900 p-3 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm">
                    Halo! Saya asisten AI untuk Manajemen Risiko. Coba tanyakan: <strong>"Bulan ini adakah risiko yang paling tinggi?"</strong>
                </div>
            </div>
        </div>

        <div class="p-3 bg-white border-t border-slate-100 flex gap-2 items-center">
            <input type="text" id="ai-chat-input" onkeypress="handleAiChatEnter(event)" placeholder="Ketik pertanyaan Anda..." class="flex-1 rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none transition shadow-inner">
            <button onclick="sendAiMessage()" class="p-2.5 rounded-xl hover:opacity-90 transition flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5" style="background-color: #4f46e5 !important; color: #ffffff !important;">
                <svg class="w-4 h-4 transform rotate-45 ml-[-2px] stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>

    {{-- Trigger Floating Button (Dipertegas) --}}
    <button onclick="toggleAiChat()" 
            class="group relative flex h-14 w-14 items-center justify-center rounded-full shadow-2xl transition-all duration-300 hover:scale-110 active:scale-95"
            style="background: linear-gradient(135deg, #4f46e5, #4338ca) !important; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5) !important;">
        
        {{-- Icon Chat --}}
        <svg class="h-7 w-7 text-white transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>

        {{-- Badge Dot / Notification Ping --}}
        <span class="absolute top-0 right-0 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 border-2 border-white"></span>
        </span>
    </button>

</div>

    {{-- SCRIPT --}}
    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            document.querySelectorAll('.kpi-card').forEach(el => {
                el.classList.remove('card-active-rose', 'card-active-indigo', 'card-active-emerald');
                el.style.borderColor = '';
                el.style.background = '';
                el.style.boxShadow = '';
            });

            const selectedContent = document.getElementById('content-' + tabName);
            if(selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            const selectedCard = document.getElementById('card-' + tabName);
            if(selectedCard) {
                if (tabName === 'top_risk') {
                    selectedCard.classList.add('card-active-rose');
                } else if (tabName === 'dep') {
                    selectedCard.classList.add('card-active-indigo');
                } else if (tabName === 'smap') {
                    selectedCard.classList.add('card-active-emerald');
                }
            }
        }

        // Fungsi untuk switch tab di dalam Top Risk
        function switchTopRiskTab(tabName) {
            document.querySelectorAll('.toprisk-tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            const selectedContent = document.getElementById('toprisk-' + tabName);
            if(selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            document.querySelectorAll('.tab-nav-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('text-slate-800');
                btn.classList.add('text-slate-400');
            });

            const selectedBtn = document.getElementById('tab-' + tabName);
            if(selectedBtn) {
                selectedBtn.classList.add('active');
                selectedBtn.classList.remove('text-slate-400');
                selectedBtn.classList.add('text-slate-800');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>
</x-admin-layout>