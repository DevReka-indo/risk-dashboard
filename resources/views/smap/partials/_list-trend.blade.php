<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Grafik Tren Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Nilai risiko Triwulan ini dibandingkan dengan Nilai risiko inherent.</p>
    </div>

    <div class="relative w-full min-h-[250px]">
        <canvas id="chartTrenRisikoHorizontal"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvasElement = document.getElementById('chartTrenRisikoHorizontal');
        if (!canvasElement) return;

        let existingChart = Chart.getChart(canvasElement);
        if (existingChart) {
            existingChart.destroy();
        }

        const ctxTrend = canvasElement.getContext('2d');

        const labelTren = {!! json_encode($trendLabels) !!};
        const dataTren = {!! json_encode($trendData) !!};

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: labelTren,
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: dataTren,
                    backgroundColor: [
                        '#ef4444',
                        '#10b981',
                        '#64748b'
                    ],
                    borderRadius: 6,
                    barThickness: 20,
                }]
            },
            options: {
                indexAxis: 'y', 
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { display: true }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
