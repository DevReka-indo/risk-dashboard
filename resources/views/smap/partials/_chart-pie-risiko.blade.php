<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6"
     x-data="{ showAll: false }">

    {{-- TOP BAR: JUDUL + TOGGLE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Visualisasi Peta Risiko SMAP</h3>
            <p class="text-xs text-slate-500 mt-0.5">Analisis perbandingan distribusi tingkat risiko awal tahun, current berjalan, dan target.</p>
        </div>

        {{-- Switch Toggle On/Off --}}
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 w-fit self-end sm:self-auto">
            <span class="text-xs font-bold text-slate-600">Tampilkan Semua Kuartal</span>
            <button type="button"
                    @click="showAll = !showAll; window.updateSmapPieCharts(showAll);"
                    :class="showAll ? 'bg-indigo-600' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                <span :class="showAll ? 'translate-x-5' : 'translate-x-0'"
                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                </span>
            </button>
        </div>
    </div>

    {{-- LAYOUT 3 CANVA PIE CHART --}}
    <div class="grid gap-6 md:grid-cols-3">

        {{-- Pie 1: Inherent --}}
        <div class="flex flex-col items-center p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
            <h4 class="text-xs font-black text-slate-700 bg-white shadow-sm border border-slate-200/60 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Inherent
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold text-white transition-colors"
                      :class="showAll ? 'bg-indigo-600' : 'bg-amber-600'"
                      x-text="showAll ? 'TOTAL ALL' : 'CURRENT TW'"></span>
            </h4>
            <div class="relative w-full max-w-[220px] h-[220px]">
                <canvas id="smapChartInherent"></canvas>
            </div>
        </div>

        {{-- Pie 2: Current --}}
        <div class="flex flex-col items-center p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
            <h4 class="text-xs font-black text-slate-700 bg-white shadow-sm border border-slate-200/60 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Current
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold text-white transition-colors"
                      :class="showAll ? 'bg-indigo-600' : 'bg-amber-600'"
                      x-text="showAll ? 'TOTAL ALL' : 'CURRENT TW'"></span>
            </h4>
            <div class="relative w-full max-w-[220px] h-[220px]">
                <canvas id="smapChartCurrent"></canvas>
            </div>
        </div>

        {{-- Pie 3: Target --}}
        <div class="flex flex-col items-center p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100/50">
            <h4 class="text-xs font-black text-indigo-950 bg-white shadow-sm border border-indigo-100 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
                Jumlah Risiko Target
                <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold text-white transition-colors"
                      :class="showAll ? 'bg-indigo-600' : 'bg-emerald-600'"
                      x-text="showAll ? 'TOTAL ALL' : 'CURRENT TW'"></span>
            </h4>
            <div class="relative w-full max-w-[220px] h-[220px]">
                <canvas id="smapChartTarget"></canvas>
            </div>
        </div>

    </div>
</div>

{{-- SCRIPT DENGAN LISTENER TOGGLE YANG MEMPERBARUI 3 GRAFIK SEKALIGUS --}}
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

        const colors = ['#03B050', '#91D050', '#FFFF00', '#FFC000', '#FF0100'];

        const configTemplate = (labelData) => ({
            type: 'pie',
            data: {
                labels: pieData.labels,
                datasets: [{
                    data: labelData,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11, weight: '600' } }
                    }
                }
            }
        });

        // Inisialisasi awal ketiga grafik membaca state kuartal tunggal (.off)
        const chartInherent = new Chart(ctxInherent.getContext('2d'), configTemplate(pieData.inherent.off));
        const chartCurrent = new Chart(ctxCurrent.getContext('2d'), configTemplate(pieData.current.off));
        const chartTarget = new Chart(ctxTarget.getContext('2d'), configTemplate(pieData.target.off));

        // Fungsi Global untuk diperintah oleh tombol toggle Alpine
        window.updateSmapPieCharts = function(showAll) {
            const state = showAll ? 'on' : 'off';

            // Sinkronisasi pembaruan data serentak untuk ketiga Pie Chart sekaligus
            chartInherent.data.datasets[0].data = pieData.inherent[state];
            chartInherent.update();

            chartCurrent.data.datasets[0].data = pieData.current[state];
            chartCurrent.update();

            chartTarget.data.datasets[0].data = pieData.target[state];
            chartTarget.update();
        };
    });
</script>
