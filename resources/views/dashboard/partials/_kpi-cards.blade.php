<div class="grid grid-cols-1 gap-6 md:grid-cols-3 animate-fade-in-up">
    {{-- Card 1: Top Risk --}}
    <div id="card-top_risk" onclick="switchTab('top_risk')" class="kpi-card card-active-rose group relative cursor-pointer overflow-hidden rounded-lg border-2 border-rose-200 bg-gradient-to-br from-rose-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-rose-300 hover:shadow-xl">
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
                    {{ number_format($stats['high_risks'] ?? 0) }}
                </h2>
                <p class="text-xs text-slate-400">Risiko dengan prioritas tertinggi</p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-rose-500 text-white shadow-md shadow-rose-500/30 transition-transform duration-300 group-hover:scale-110">
                <svg class="h-6 w-6 stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-rose-600 border-t border-rose-100/60 pt-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ number_format($stats['high_risks'] ?? 0) }} risiko kritis butuh penanganan</span>
        </div>
    </div>

    {{-- Card 2: SMAP --}}
    <div id="card-smap" onclick="switchTab('smap')" class="kpi-card group relative cursor-pointer overflow-hidden rounded-lg border-2 border-purple-200 bg-gradient-to-br from-purple-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-300 hover:shadow-xl">        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-500"></span>
                        Kepatuhan
                    </span>
                </div>
                <p class="text-sm font-semibold text-slate-600">SMAP</p>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ number_format($stats['total_smap'] ?? 0) }}
                </h2>
                <p class="text-xs text-slate-400">Sistem Manajemen Anti Penyuapan</p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg shadow-md transition-transform duration-300 group-hover:scale-110" style="background-color: #9333ea !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(147, 51, 234, 0.39) !important;">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="stroke: #ffffff !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0112 2.714z" /></svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-purple-600 border-t border-purple-100/60 pt-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ number_format($stats['total_smap'] ?? 0) }} risiko aktif terpantau</span>
        </div>
    </div>

    {{-- Card 3: Departemen --}}
    <div id="card-dep" onclick="switchTab('dep')" class="kpi-card group relative cursor-pointer overflow-hidden rounded-lg border-2 border-blue-200 bg-gradient-to-br from-blue-50/80 via-white to-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl">
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
                    {{ number_format($stats['total_dep'] ?? 0) }}
                </h2>
                <p class="text-xs text-slate-400">Monitoring risiko per departemen</p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-md shadow-blue-500/30 transition-transform duration-300 group-hover:scale-110">
                <svg class="h-6 w-6 stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3a1.5 1.5 0 011.5-1.5h3a1.5 1.5 0 011.5 1.5v3" /></svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-blue-600 border-t border-blue-100/60 pt-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M3.75 4.5h16.5m-16.5 3.75h16.5" /></svg>
            <span>{{ number_format($stats['total_dep'] ?? 0) }} unit risiko terkelola</span>
        </div>
    </div>
</div>
