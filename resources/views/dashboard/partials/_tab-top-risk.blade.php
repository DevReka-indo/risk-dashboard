<div id="content-top_risk" class="tab-content space-y-6 animate-fade-in-up delay-100">
    {{-- Tab Navigation --}}
    <div class="flex items-center justify-between border-b-2 border-slate-200 pb-0">
        <div class="flex gap-6">
            <button onclick="switchTopRiskTab('analisis')" id="tab-analisis" class="tab-nav-btn active pb-3 text-sm font-medium text-slate-800">
                Analisis & Daftar
            </button>
            <button onclick="switchTopRiskTab('heatmap')" id="tab-heatmap" class="tab-nav-btn pb-3 text-sm font-medium text-slate-400 hover:text-slate-600">
                Heatmap Risiko
            </button>
        </div>
    </div>

    {{-- Tab Content: Analisis --}}
    <div id="toprisk-analisis" class="toprisk-tab-content space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" /></svg></div>
                    <h3 class="text-sm font-bold text-slate-800">Distribusi Level Risiko</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">Persentase tingkat bahaya dari seluruh risiko teridentifikasi.</p>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="levelChart" data-level-distribution="{{ json_encode($levelDistribution) }}"></canvas>
                </div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41L11 3.83a2 2 0 00-2.83 0L3.83 8.17a2 2 0 000 2.83L13.41 20.6a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.84z" /><circle cx="7.5" cy="7.5" r="1.3" fill="currentColor" /></svg></div>
                    <h3 class="text-sm font-bold text-slate-800">Kategori Risiko Terbanyak</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">Konsentrasi isu berdasarkan kelompok risiko.</p>
                <div class="relative h-64 w-full">
                    <canvas id="categoryChart" data-categories="{{ json_encode($riskCategories) }}"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel Top Risiko --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-rose-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="border-b border-rose-100 bg-gradient-to-r from-rose-50/50 to-white p-5">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
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
                                    <p class="font-semibold text-slate-800 line-clamp-1">{{ $risk->risk_event_deta }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $risk->unitKerja->nama_unit ?? 'Unit Umum' }}</p>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="inline-flex whitespace-nowrap items-center gap-1 rounded-lg bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
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

            <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="border-b border-slate-100 p-5">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
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
                                    <p class="text-xs text-indigo-600 mt-1">{{ $update->updated_at->diffForHumans() }}</p>
                                </td>
                                <td class="p-4 text-right">
                                    @if($update->penanganan == 'Sudah')
                                        <span class="inline-flex whitespace-nowrap items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Selesai</span>
                                    @elseif($update->penanganan == 'Proses')
                                        <span class="inline-flex whitespace-nowrap items-center gap-1 rounded-lg bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Diproses</span>
                                    @else
                                        <span class="inline-flex whitespace-nowrap items-center gap-1 rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Belum</span>
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
