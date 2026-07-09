<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-base font-bold text-slate-900">Grafik Tren Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Nilai risiko Triwulan ini dibandingkan dengan Nilai risiko inherent.</p>
    </div>

    <div class="relative w-full min-h-[220px]">
        <canvas id="chartTrenRisikoHorizontal"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let existingChart = Chart.getChart("chartTrenRisikoHorizontal");
        if (existingChart != undefined) {
            existingChart.destroy();
        }

        const ctxTrend = document.getElementById('chartTrenRisikoHorizontal').getContext('2d');

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: {!! json_encode($trendData) !!},
                    backgroundColor: [
                        '#f59e0b', // Amber/Kuning -> Naik
                        '#65a30d', // Lime/Hijau -> Turun
                        '#ea580c'  // Orange/Jingga -> Stagnan
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 24, // Ketebalan bar disesuaikan agar lebih proporsional
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans, sans-serif', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans, sans-serif' }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#64748b',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            color: '#334155',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 12, weight: '600' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
