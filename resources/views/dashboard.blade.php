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
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
        .animate-scale-in { animation: scaleIn 0.4s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        .dashboard-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        .dashboard-card:hover {
            transform: translateY(-6px);
        }
        .dashboard-card .card-icon {
            transition: all 0.3s ease;
        }
        .dashboard-card:hover .card-icon {
            transform: scale(1.1) rotate(-5deg);
        }
        .dashboard-card .progress-bar {
            transition: all 0.5s ease;
        }
        .dashboard-card:hover .progress-bar {
            width: 100% !important;
        }
    </style>

    <div x-data="{ 
        selected: 'home',
        activeTab: '{{ request()->query('dashboard', 'home') }}'
    }" x-init="selected = activeTab">
        
        {{-- HOME DASHBOARD (3 Card Utama) --}}
        <div x-show="selected === 'home'" x-transition:enter.duration.400ms.opacity>
            
            <div class="text-center mb-10 animate-fade-in-up">
                <h2 class="text-2xl font-bold text-slate-800">Risk Monitoring</h2>
                <p class="text-sm text-slate-500 mt-1">Pilih dashboard yang ingin Anda lihat</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                
                {{-- Card SMAP --}}
                <div @click="selected = 'smap'" 
                     class="dashboard-card rounded-3xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50/80 to-white p-6 shadow-lg shadow-indigo-500/10 cursor-pointer hover:shadow-2xl hover:shadow-indigo-500/20">
                    <div class="flex items-start gap-4">
                        <div class="card-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30 flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0 1 12 2.714Z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-slate-800">SMAP</h3>
                            <p class="text-xs text-indigo-600 font-medium">Sistem Manajemen Risiko</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                            {{ number_format($stats['total_smap'] ?? 0) }} Risiko
                        </span>
                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                            Aktif
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Monitoring risiko SMAP secara komprehensif</p>
                    <div class="mt-4 w-full h-1.5 bg-indigo-100 rounded-full overflow-hidden">
                        <div class="progress-bar h-full w-3/4 bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-full"></div>
                    </div>
                </div>

                {{-- Card Top Risk --}}
                <div @click="selected = 'toprisk'" 
                     class="dashboard-card rounded-3xl border-2 border-rose-200 bg-gradient-to-br from-rose-50/80 to-white p-6 shadow-lg shadow-rose-500/10 cursor-pointer hover:shadow-2xl hover:shadow-rose-500/20">
                    <div class="flex items-start gap-4">
                        <div class="card-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/30 flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 2 5-7" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 7h1.5V8.5" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-slate-800">Top Risk</h3>
                            <p class="text-xs text-rose-600 font-medium">Risiko Prioritas</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">
                            {{ number_format($stats['total_top'] ?? 0) }} Risiko
                        </span>
                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                            Kritis
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Risiko dengan prioritas tertinggi</p>
                    <div class="mt-4 w-full h-1.5 bg-rose-100 rounded-full overflow-hidden">
                        <div class="progress-bar h-full w-2/3 bg-gradient-to-r from-rose-500 to-rose-400 rounded-full"></div>
                    </div>
                </div>

                {{-- Card Department --}}
                <div @click="selected = 'department'" 
                     class="dashboard-card rounded-3xl border-2 border-blue-200 bg-gradient-to-br from-blue-50/80 to-white p-6 shadow-lg shadow-blue-500/10 cursor-pointer hover:shadow-2xl hover:shadow-blue-500/20">
                    <div class="flex items-start gap-4">
                        <div class="card-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H4.125A1.125 1.125 0 0 0 3 3.375v17.25c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125v-6.375Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11.25h6m-6 4h6m-6-8h2.25" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-slate-800">Department</h3>
                            <p class="text-xs text-blue-600 font-medium">Monitoring Departemen</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                            {{ number_format($stats['total_dep'] ?? 0) }} Risiko
                        </span>
                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                            Terpantau
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Monitoring risiko per departemen</p>
                    <div class="mt-4 w-full h-1.5 bg-blue-100 rounded-full overflow-hidden">
                        <div class="progress-bar h-full w-4/5 bg-gradient-to-r from-blue-500 to-blue-400 rounded-full"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DASHBOARD SMAP                                               --}}
        {{-- ============================================================ --}}
        <div x-show="selected === 'smap'" x-transition:enter.duration.400ms.opacity>
            
            {{-- Tombol Kembali --}}
            <button @click="selected = 'home'" 
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition mb-6 group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Dashboard
            </button>

            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-indigo-100">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0 1 12 2.714Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">SMAP Dashboard</h2>
                        <p class="text-sm text-slate-500">Monitoring risiko SMAP secara komprehensif</p>
                    </div>
                </div>

                {{-- KPI SMAP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Risiko SMAP</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total_smap']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-indigo-700">{{ number_format($stats['total_smap']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Perlu Tindakan</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($stats['pending_actions']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-700">0</p>
                    </div>
                </div>

                {{-- Placeholder untuk konten SMAP --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Dashboard SMAP</h3>
                    <p class="text-xs text-slate-500 mb-6">Data dan visualisasi SMAP</p>
                    <div class="p-12 text-center text-slate-400 border-2 border-dashed border-slate-200 rounded-2xl">
                        <svg class="h-12 w-12 mx-auto text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="mt-2 text-sm">Data SMAP akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DASHBOARD TOP RISK                                           --}}
        {{-- ============================================================ --}}
        <div x-show="selected === 'toprisk'" x-transition:enter.duration.400ms.opacity>
            
            {{-- Tombol Kembali --}}
            <button @click="selected = 'home'" 
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-rose-600 transition mb-6 group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Dashboard
            </button>

            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-rose-100">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 2 5-7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 7h1.5V8.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Top Risk Dashboard</h2>
                        <p class="text-sm text-slate-500">Risiko dengan prioritas tertinggi</p>
                    </div>
                </div>

                {{-- KPI Top Risk --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Risiko</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total_top']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">High Risk</p>
                        <p class="mt-2 text-3xl font-bold text-rose-700">{{ number_format($stats['high_risks']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Perlu Tindakan</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($stats['pending_actions']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-700">0</p>
                    </div>
                </div>

                {{-- Top 5 High Risks --}}
                <div class="rounded-3xl border border-rose-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-rose-50/30 p-5">
                        <h3 class="text-sm font-bold text-rose-800">Top 5 Risiko Kritis</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Memerlukan perhatian dan mitigasi segera</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <tbody class="divide-y divide-slate-100">
                                @forelse($topHighRisks as $risk)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800 line-clamp-1">{{ $risk->risk_event_deta }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $risk->unitKerja->nama_unit ?? 'Unit Umum' }}</p>
                                    </td>
                                    <td class="p-4 text-right">
                                        <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700">Skor: {{ $risk->value ?? '-' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="p-6 text-center text-slate-400">Belum ada risiko kategori kritis.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DASHBOARD DEPARTMENT                                        --}}
        {{-- ============================================================ --}}
        <div x-show="selected === 'department'" x-transition:enter.duration.400ms.opacity>
            
            {{-- Tombol Kembali --}}
            <button @click="selected = 'home'" 
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition mb-6 group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Dashboard
            </button>

            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H4.125A1.125 1.125 0 0 0 3 3.375v17.25c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125v-6.375Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 11.25h6m-6 4h6m-6-8h2.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Department Dashboard</h2>
                        <p class="text-sm text-slate-500">Monitoring risiko per departemen</p>
                    </div>
                </div>

                {{-- KPI Department --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Risiko</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total_dep']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($stats['total_dep']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Perlu Tindakan</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($stats['pending_actions']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-700">0</p>
                    </div>
                </div>

                {{-- Recent Updates --}}
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-5">
                        <h3 class="text-sm font-bold text-slate-800">Pembaruan Terakhir</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Log aktivitas perubahan data atau monitoring</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentUpdates as $update)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-4">
                                        <p class="font-semibold text-slate-800 line-clamp-1">{{ $update->risk_event_deta }}</p>
                                        <p class="text-xs text-indigo-600 mt-1">{{ $update->updated_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($update->penanganan == 'Sudah')
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Selesai</span>
                                        @elseif($update->penanganan == 'Proses')
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Diproses</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Belum</span>
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

        {{-- ============================================================ --}}
        {{-- AI CHAT WIDGET (Tetap di semua tampilan)                     --}}
        {{-- ============================================================ --}}
        <div id="ai-chat-widget" data-csrf="{{ csrf_token() }}" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
            <div id="ai-chat-window" class="hidden mb-4 w-80 sm:w-96 rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col h-[28rem] transition-all duration-300 transform origin-bottom-right">
                <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm">Risk AI Assistant</h3>
                            <p class="text-[10px] text-indigo-200">Online</p>
                        </div>
                    </div>
                    <button onclick="toggleAiChat()" class="text-indigo-200 hover:text-white transition rounded-full hover:bg-white/10 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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
                    <input type="text" id="ai-chat-input" onkeypress="handleAiChatEnter(event)" placeholder="Ketik pertanyaan Anda..." class="flex-1 rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none transition shadow-inner">
                    <button onclick="sendAiMessage()" class="bg-indigo-600 text-white p-2.5 rounded-2xl hover:bg-indigo-700 transition flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 transform rotate-45 ml-[-2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </div>
            </div>
            <button onclick="toggleAiChat()" class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all duration-300 flex items-center justify-center transform hover:scale-105 group relative">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <span class="absolute top-0 right-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
                </span>
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>
</x-admin-layout>