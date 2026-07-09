<div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-col gap-1">
        <h2 class="text-base font-bold text-slate-900">
            Grafik Distribusi Risiko per Departemen
        </h2>
        <p class="text-sm text-slate-500">
            Jumlah risiko berdasarkan jabatan/unit kerja yang ditapis.
        </p>
    </div>

    <div class="relative w-full" style="height: 350px;">
        <canvas id="departmentRiskChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('departmentRiskChart').getContext('2d');

        // Data dari Controller ditukar kepada format Javascript
        const labels = {!! json_encode($chartLabels ?? []) !!};
        const dataValues = {!! json_encode($chartData ?? []) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: dataValues,
                    backgroundColor: 'rgba(79, 70, 229, 0.85)', // Warna Indigo (Biru)
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
