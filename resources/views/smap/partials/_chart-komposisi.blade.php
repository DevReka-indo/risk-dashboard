<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Komposisi Risk Owner atas Distribusi Level Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Jumlah event risiko per departemen berdasarkan tingkat level risiko (Hanya menampilkan departemen yang memiliki data risiko).</p>
    </div>

    <div class="relative w-full min-h-[500px]">
        <canvas id="chartKomposisiRiskOwner"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let existingChart = Chart.getChart("chartKomposisiRiskOwner");
        if (existingChart != undefined) {
            existingChart.destroy();
        }

        const ctxStacked = document.getElementById('chartKomposisiRiskOwner').getContext('2d');
        const labelsData = {!! json_encode($labels) !!};

        // Mengatur tinggi container canvas secara dinamis berdasarkan jumlah departemen
        const chartHeight = Math.max(300, labelsData.length * 55);
        ctxStacked.canvas.parentNode.style.height = chartHeight + 'px';

        new Chart(ctxStacked, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: {!! json_encode($chartDatasets) !!}
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        // 🔥 Kembalikan padding ke standar kecil agar sumbu Y bergeser penuh ke kiri card
                        left: 20,
                        right: 20,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: {
                            autoSkip: false,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            // 🔥 Biarkan text merapat normal ke sumbu kiri dan tidak memaksa membuat ruang kosong raksasa
                            padding: 12
                        }
                    }
                }
            }
        });
    });
</script>
