<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm space-y-6">

    {{-- TOP BAR: JUDUL --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Visualisasi Peta Risiko SMAP</h3>
            <p class="text-sm text-slate-500 mt-0.5">Analisis komparatif sebaran tingkat risiko inherent awal tahun, current berjalan, dan target target.</p>
        </div>
    </div>

    {{-- LAYOUT 3 CANVA PIE CHART BERJEJER (TANPA CARD) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-4">

        {{-- Pie 1: Inherent --}}
        <!-- Card style dihapus, hanya menyisakan flex untuk perataan tengah -->
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Jumlah Risiko Inherent
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="smapChartInherent"></canvas>
            </div>
        </div>

        {{-- Pie 2: Current --}}
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Jumlah Risiko Current
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="smapChartCurrent"></canvas>
            </div>
        </div>

        {{-- Pie 3: Target --}}
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Jumlah Risiko Target
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="smapChartTarget"></canvas>
            </div>
        </div>

    </div>

    {{-- KETERANGAN WARNA LEGENDA UTAMA --}}
    <div class="pt-2 border-t border-slate-100 flex flex-wrap justify-center items-center gap-x-6 gap-y-3 text-xs font-medium text-slate-600">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#03B050] shadow-sm"></span> Low</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#91D050] shadow-sm"></span> Low Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFFF00] border border-slate-200 shadow-sm"></span> Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFC000] shadow-sm"></span> Moderate to High</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FF0100] shadow-sm"></span> High</div>
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
                    borderWidth: 2,
                    borderColor: '#ffffff' // Border putih memisahkan irisan pie agar terlihat elegan tanpa card
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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

        new Chart(ctxInherent.getContext('2d'), configTemplate(pieData.inherent));
        new Chart(ctxCurrent.getContext('2d'), configTemplate(pieData.current));
        new Chart(ctxTarget.getContext('2d'), configTemplate(pieData.target));
    });
</script>