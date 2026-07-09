<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-col gap-1">
        <h2 class="text-lg font-bold text-slate-900">Grafik Risiko per Departemen</h2>
        <p class="text-sm text-slate-500">Jumlah risiko berdasarkan jabatan/unit kerja yang difilter.</p>
    </div>

    <div class="relative w-full min-h-[400px]">
        <canvas id="departmentRiskChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let existingChart = Chart.getChart("departmentRiskChart");
        if (existingChart != undefined) existingChart.destroy();

        const ctx = document.getElementById('departmentRiskChart').getContext('2d');
        const labelsData = {!! json_encode($labels ?? []) !!};
        const dataValues = {!! json_encode($data ?? []) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [{
                    label: 'Total Risiko',
                    data: dataValues,
                    backgroundColor: '#4f46e5',
                    borderRadius: 8,
                    barThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: false, maxRotation: 45, minRotation: 45, font: { size: 11 } }
                    },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
