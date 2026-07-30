<div class="rounded-lg border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
    <h2 class="text-xl font-bold text-slate-900 mb-8">Grafik Risiko per Departemen (SMAP)</h2>

    <!-- Wrapper disamakan persis tinggi 500px dan min-h-[500px] -->
    <div class="relative w-full h-[500px] min-h-[500px] flex items-center justify-center" id="smapChartContainer">
        <canvas id="chartDepartemenBaru"></canvas>

        <!-- Peringatan Data Kosong (Bila Tidak Ada Data SMAP) -->
        <div id="emptySmapChartMessage" class="absolute hidden text-center text-slate-400 font-medium">
            <svg class="mx-auto h-14 w-14 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Belum ada data risiko SMAP pada periode triwulan dan tahun ini.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        initSmapChart();
    });

    function initSmapChart() {
        const canvas = document.getElementById('chartDepartemenBaru');
        if (!canvas) return;

        // 🛑 BENTENG UTAMA: Cek apakah container tab SMAP sedang tersembunyi (hidden)
        // Jika parent container SMAP sedang hidden, HENTIKAN RENDER CHART SMAP!
        if (canvas.closest('.hidden') !== null || canvas.offsetParent === null) {
            return;
        }

        // Ambil data spesifik SMAP
        const labels = {!! json_encode($smapData['labels'] ?? $labels ?? []) !!};
        const dataValues = {!! json_encode($smapData['data'] ?? $data ?? []) !!};

        const emptyMessage = document.getElementById('emptySmapChartMessage');
        const hasData = Array.isArray(dataValues) && dataValues.some(val => Number(val) > 0);

        if (labels.length === 0 || !hasData) {
            canvas.style.display = 'none';
            if (emptyMessage) emptyMessage.classList.remove('hidden');
            return;
        }

        canvas.style.display = 'block';
        if (emptyMessage) emptyMessage.classList.add('hidden');

        // Hancurkan instance chart lama jika ada
        let existingChart = Chart.getChart("chartDepartemenBaru");
        if (existingChart) existingChart.destroy();

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Risiko SMAP',
                    data: dataValues,
                    backgroundColor: '#6b21a8', // Warna ungu khas SMAP
                    maxBarThickness: 70,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9,
                    borderRadius: { topLeft: 6, topRight: 6 }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
</script>
