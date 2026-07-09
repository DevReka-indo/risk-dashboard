<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-col gap-1">
        <h2 class="text-base font-bold text-slate-900">
            Grafik Distribusi Risiko per Departemen
        </h2>
        <p class="text-sm text-slate-500">
            Jumlah risiko berdasarkan jabatan/unit kerja yang difilter.
        </p>
    </div>

    <div class="relative w-full" style="height: 380px;">
        <canvas id="departmentRiskChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartElement = document.getElementById('departmentRiskChart');
        if (!chartElement) return;

        const ctx = chartElement.getContext('2d');
        const labels = {!! json_encode($chartLabels ?? []) !!};
        const dataValues = {!! json_encode($chartData ?? []) !!};

        // Gradasi warna agar grafik tampak modern & premium
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.9)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.4)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: ' Total Risiko',
                    data: dataValues,
                    backgroundColor: gradient,
                    hoverBackgroundColor: 'rgba(67, 56, 202, 1)',
                    borderRadius: 6,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, family: "'Inter', sans-serif" },
                        bodyFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 11 },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            stepSize: 1,
                            font: { family: "'Inter', sans-serif", size: 12 },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });
</script>
