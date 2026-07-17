<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Grafik Kategori Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Distribusi total jumlah risiko yang terdaftar berdasarkan kategori.</p>
    </div>

    <div class="relative w-full min-h-[350px]">
        <canvas id="chartKategoriBar"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let existingChart = Chart.getChart("chartKategoriBar");
        if (existingChart != undefined) existingChart.destroy();

        const ctxCat = document.getElementById('chartKategoriBar').getContext('2d');

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: {!! json_encode($catLabels) !!},
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: {!! json_encode($catData) !!},
                    backgroundColor: '#10b981',
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
                    y: { grid: { display: false }, ticks: { autoSkip: false } }
                }
            }
        });
    });
</script>
