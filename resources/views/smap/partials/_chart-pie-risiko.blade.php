<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">

    {{-- TOP BAR: JUDUL (TOMBOL TOGGLE DIHAPUS BERSIH) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Visualisasi Peta Risiko SMAP</h3>
            <p class="text-xs text-slate-500 mt-0.5">Analisis komparatif sebaran tingkat risiko inherent awal tahun, current berjalan, dan target target.</p>
        </div>
        <span class="inline-flex items-center rounded-xl bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 self-start sm:self-center">
            Live Monitoring
        </span>
    </div>

    {{-- LAYOUT 3 CANVA PIE CHART BERJEJER --}}
    <div class="grid gap-6 md:grid-cols-3">

        {{-- Pie 1: Inherent (Selalu Tetap / Baseline Master) --}}
        <div class="flex flex-col items-center p-5 bg-slate-50/50 rounded-2xl border border-slate-100">
            <h4 class="text-xs font-black text-slate-700 bg-white shadow-sm border border-slate-200/60 px-3 py-1.5 rounded-xl mb-6 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Inherent
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold bg-slate-800 text-white">FIXED BASELINE</span>
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px]">
                <canvas id="smapChartInherent"></canvas>
            </div>
        </div>

        {{-- Pie 2: Current (Dinamis Berubah Mengikuti Filter Kuartal) --}}
        <div class="flex flex-col items-center p-5 bg-amber-50/20 rounded-2xl border border-amber-100/60">
            <h4 class="text-xs font-black text-amber-900 bg-white shadow-sm border border-amber-200/60 px-3 py-1.5 rounded-xl mb-6 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Current
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold bg-amber-600 text-white">DYNAMIC FILTER</span>
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px]">
                <canvas id="smapChartCurrent"></canvas>
            </div>
        </div>

        {{-- Pie 3: Target (Selalu Tetap / Baseline Master) --}}
        <div class="flex flex-col items-center p-5 bg-indigo-50/30 rounded-2xl border border-indigo-100/50">
            <h4 class="text-xs font-black text-indigo-950 bg-white shadow-sm border border-indigo-100 px-3 py-1.5 rounded-xl mb-6 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Target
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold bg-indigo-600 text-white">FIXED TARGET</span>
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px]">
                <canvas id="smapChartTarget"></canvas>
            </div>
        </div>

    </div>

    {{-- KETERANGAN WARNA LEGENDA UTAMA DI BAWAH CARD GRAFIK --}}
    <div class="pt-4 border-t border-slate-100 flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-xs font-bold text-slate-600">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#03B050]"></span> Low</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#91D050]"></span> Low Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFFF00] border border-slate-200"></span> Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFC000]"></span> Moderate to High</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FF0100]"></span> High</div>
    </div>
</div>

{{-- SCRIPT PENGECATAN CHART.JS SECARA SERENTAK --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const pieData = {!! json_encode($smapPieData ?? null) !!};

        if (!pieData || !pieData.labels) return;

        const ctxInherent = document.getElementById('smapChartInherent');
        const ctxCurrent = document.getElementById('smapChartCurrent');
        const ctxTarget = document.getElementById('smapChartTarget');

        if (!ctxInherent || !ctxCurrent || !ctxTarget) return;

        // Bersihkan instance lama agar tidak terjadi penumpukan memori
        if (Chart.getChart(ctxInherent)) Chart.getChart(ctxInherent).destroy();
        if (Chart.getChart(ctxCurrent)) Chart.getChart(ctxCurrent).destroy();
        if (Chart.getChart(ctxTarget)) Chart.getChart(ctxTarget).destroy();

        // Indikator Warna Level Risiko Baku
        const colors = ['#03B050', '#91D050', '#FFFF00', '#FFC000', '#FF0100'];

        const configTemplate = (labelData) => ({
            type: 'pie',
            data: {
                labels: pieData.labels,
                datasets: [{
                    data: labelData,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Menggunakan legenda HTML di bawah agar tampilan lingkaran grafiknya luas
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${label}: ${value} Risiko (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gambar data ke masing-masing canvas chart secara terpisah
        new Chart(ctxInherent.getContext('2d'), configTemplate(pieData.inherent));
        new Chart(ctxCurrent.getContext('2d'), configTemplate(pieData.current));
        new Chart(ctxTarget.getContext('2d'), configTemplate(pieData.target));
    });
</script>
