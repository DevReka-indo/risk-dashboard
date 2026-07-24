<div class="rounded-lg border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
    <h2 class="text-xl font-bold text-slate-900 mb-8">Grafik Risiko per Departemen (SMAP)</h2>

    <!-- Wrapper disamakan persis tinggi 500px dan min-h-[500px] -->
    <div class="relative w-full h-[500px] min-h-[500px] flex items-center justify-center" id="smapChartContainer">
        <canvas id="chartDepartemenBaru"></canvas>

        <!-- Peringatan Data Kosong (Bila Tidak Ada Data SMAP) -->
        <div id="emptySmapChartMessage" class="absolute hidden text-center text-slate-400 font-medium">
            <svg class="mx-auto h-14 w-14 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Belum ada data risiko SMAP pada periode triwulan dan tahun ini.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labels = {!! json_encode($labels ?? []) !!};
        const dataValues = {!! json_encode($data ?? []) !!};

        const canvas = document.getElementById('chartDepartemenBaru');
        const emptyMessage = document.getElementById('emptySmapChartMessage');

        if (!canvas) return;

        // Cek data ketersediaan
        const hasData = dataValues.some(val => val > 0);

        if (labels.length === 0 || !hasData) {
            canvas.style.display = 'none';
            if (emptyMessage) emptyMessage.classList.remove('hidden');
            return;
        }

        canvas.style.display = 'block';
        if (emptyMessage) emptyMessage.classList.add('hidden');

        // Hapus instance lama jika ada (mencegah glitch/duplicate saat tab diganti/direload)
        let existingChart = Chart.getChart("chartDepartemenBaru");
        if (existingChart) existingChart.destroy();

        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: dataValues,
                    backgroundColor: '#4f46e5', // Warna Purple Khas SMAP (atau pakai #4f46e5 jika mau sama persis)
                    maxBarThickness: 70,        // Batang bar dibuat montok & proporsional
                    barPercentage: 0.8,
                    categoryPercentage: 0.9,
                    borderRadius: {
                        topLeft: 6,
                        topRight: 6,
                        bottomLeft: 0,
                        bottomRight: 0
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 10, bottom: 10 }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 14,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Total Risiko SMAP: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0,
                            font: { size: 12 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#e2e8f0',
                            drawTicks: false,
                            borderDash: [5, 5] // Garis horizontal putus-putus
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    });
</script>
