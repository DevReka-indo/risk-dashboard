<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 text-center">
        <h3 class="text-base font-semibold uppercase tracking-wider text-slate-800">
            Efektivitas Penanganan Risiko
        </h3>
    </div>

    <div class="relative w-full" style="height: 300px;">
        <canvas id="effectivenessChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($effectivenessDistribution);

        const ctx = document.getElementById('effectivenessChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: ['#a3e635', '#f87171', '#fbbf24', '#94a3b8'], // Hijau, Merah, Kuning, Abu
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { padding: 20 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
