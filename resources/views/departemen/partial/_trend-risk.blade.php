<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
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
        let existingChart = Chart.getChart("chartTrenRisikoHorizontal");
        if (existingChart != undefined) existingChart.destroy();

        const ctxTrend = document.getElementById('chartTrenRisikoHorizontal').getContext('2d');
        const labels = {!! json_encode($trendLabels) !!};
        const dataValues = {!! json_encode($trendData) !!};

        const dynamicColors = labels.map(label => {
            if (label === 'Naik') return '#f59e0b';
            if (label === 'Turun') return '#65a30d';
            if (label === 'Stabil' || label === 'Stagnan') return '#ea580c';
            return '#cbd5e1';
        });

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: dataValues,
                    backgroundColor: dynamicColors,
                    borderRadius: 6,
                    barThickness: 16,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } },
                    y: { grid: { display: false } }
                }
            }
        });
    });
</script>
