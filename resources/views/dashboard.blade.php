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
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>

    <div class="space-y-6 pb-10">

        {{-- 1. KPI CARDS (HIGHLIGHT METRIK UTAMA) --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 animate-fade-in-up">

            {{-- Card Total Risiko --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Risiko</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total_risks']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    </div>
                </div>
            </div>

            {{-- Card Risiko Tinggi (Fokus Perhatian) --}}
            <div class="rounded-3xl border border-rose-200 bg-rose-50/50 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Risiko Tinggi & Kritis</p>
                        <p class="mt-2 text-3xl font-bold text-rose-700">{{ number_format($stats['high_risks']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-200 text-rose-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                </div>
            </div>

            {{-- Card Menunggu Tindakan (Pending) --}}
            <div class="rounded-3xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu Tindakan</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($stats['pending_actions']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-200 text-amber-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>

            {{-- Card Modul Distribusi --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md flex flex-col justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Sebaran Modul</p>
                <div class="flex items-center justify-between mt-auto">
                    <div class="text-center"><p class="text-lg font-bold text-slate-800">{{ $stats['total_dep'] }}</p><p class="text-[10px] font-semibold text-slate-400">DEP</p></div>
                    <div class="h-6 w-px bg-slate-200"></div>
                    <div class="text-center"><p class="text-lg font-bold text-slate-800">{{ $stats['total_smap'] }}</p><p class="text-[10px] font-semibold text-slate-400">SMAP</p></div>
                    <div class="h-6 w-px bg-slate-200"></div>
                    <div class="text-center"><p class="text-lg font-bold text-slate-800">{{ $stats['total_top'] }}</p><p class="text-[10px] font-semibold text-slate-400">TOP</p></div>
                </div>
            </div>
        </div>

        {{-- 2. GRAFIK (VISUALISASI DATA) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 animate-fade-in-up delay-100">

            {{-- Kategori Risiko Terbanyak (Bar Chart) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-1">Kategori Risiko Terbanyak</h3>
                <p class="text-xs text-slate-500 mb-6">Konsentrasi isu berdasarkan kelompok risiko.</p>
                <div class="relative h-64 w-full">
                    {{-- Mengirim data risiko lewat attribute 'data-categories' --}}
                    <canvas id="categoryChart" data-categories="{{ json_encode($riskCategories) }}"></canvas>
                </div>
            </div>

            {{-- Proporsi Level Risiko (Donut Chart) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-1">Distribusi Level Risiko</h3>
                <p class="text-xs text-slate-500 mb-6">Persentase tingkat bahaya dari seluruh data.</p>
                <div class="relative h-64 w-full flex justify-center">
                    {{-- Mengirim data sebaran level lewat attribute 'data-level-distribution' --}}
                    <canvas id="levelChart" data-level-distribution="{{ json_encode($levelDistribution) }}"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. ACTIONABLE TABLES (DAFTAR SEGERA) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 animate-fade-in-up delay-200">

            {{-- Top 5 High Risks --}}
            <div class="rounded-3xl border border-rose-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-rose-50/30 p-5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-rose-600">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-bold text-rose-800">Top 5 Risiko Kritis</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Memerlukan perhatian dan mitigasi segera.</p>
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

            {{-- Recent Updates (Log Aktivitas Terakhir) --}}
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 p-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <h3 class="text-sm font-bold text-slate-800">Pembaruan Terakhir</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Log aktivitas perubahan data atau monitoring.</p>
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

    {{-- START: WIDGET CHAT AI (FLOATING)           --}}
    {{-- Mengirim CSRF token lewat attribute 'data-csrf' --}}
    <div id="ai-chat-widget" data-csrf="{{ csrf_token() }}" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">

        {{-- Kotak Chat (Awalnya Disembunyikan) --}}
        <div id="ai-chat-window" class="hidden mb-4 w-80 sm:w-96 rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col h-[28rem] transition-all duration-300 transform origin-bottom-right">

            {{-- Header Chat --}}
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

            {{-- Area Pesan --}}
            <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 text-sm scroll-smooth">
                {{-- Pesan Pembuka Default dari AI --}}
                <div class="flex items-start gap-2">
                    <div class="bg-indigo-100 text-indigo-900 p-3 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm">
                        Halo! Saya asisten AI untuk Manajemen Risiko. Coba tanyakan: <strong>"Bulan ini adakah risiko yang paling tinggi?"</strong>
                    </div>
                </div>
            </div>

            {{-- Area Input --}}
            <div class="p-3 bg-white border-t border-slate-100 flex gap-2 items-center">
                <input type="text" id="ai-chat-input" onkeypress="handleAiChatEnter(event)" placeholder="Ketik pertanyaan Anda..." class="flex-1 rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none transition shadow-inner">
                <button onclick="sendAiMessage()" class="bg-indigo-600 text-white p-2.5 rounded-2xl hover:bg-indigo-700 transition flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 transform rotate-45 ml-[-2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </div>
        </div>

        {{-- Tombol Bulat Melayang (Toggle) --}}
        <button onclick="toggleAiChat()" class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all duration-300 flex items-center justify-center transform hover:scale-105 group relative">
            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            {{-- Badge Notifikasi Merah --}}
            <span class="absolute top-0 right-0 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
            </span>
        </button>
    </div>

    {{-- END: WIDGET CHAT AI                        --}}
    {{-- KONTEN UTAMA SELESAI, HUBUNGKAN FILE JS EKSTERNAL DI BAWAH INI --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>
</x-admin-layout>
