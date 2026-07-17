<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 text-center">
        <h3 class="text-base font-semibold uppercase tracking-wider text-slate-800">
            Progres Penanganan Risiko
        </h3>
    </div>

    <div class="relative w-full" style="height: 300px;">
        <canvas id="progressChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($progressDistribution);

        const ctx = document.getElementById('progressChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: chartData.colors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            font: { size: 14 }
                        }
                    }
                }
            }
        });
    });
</script>
