<!-- Container Chart Keberhasilan Penanganan Risiko SMAP -->
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-base font-bold text-slate-900">Keberhasilan Penanganan Risiko SMAP</h3>
    <div class="h-64">
        <canvas id="smapChartEfektif"></canvas>
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const canvasElement = document.getElementById('smapChartEfektif');
            if (!canvasElement) return;

            // Membaca data pie dari controller SMAP
            const pieData = {!! json_encode($smapPieData ?? null) !!};

            // Mengambil array data triwulan berjalan (.off) atau fallback array default
            const efektifValues = (pieData && pieData.efektif && pieData.efektif.off)
                ? pieData.efektif.off
                : {!! json_encode($efektifRisikoData ?? [0, 0, 0, 0, 0, 0]) !!};

            // Memastikan label menggunakan urutan standar yang sama
            const chartLabels = (pieData && pieData.efektif && pieData.efektif.labels)
                ? pieData.efektif.labels
                : [
                    'Effective',
                    'Mostly Effective',
                    'Partially Effective',
                    'In-Effective',
                    'Pencatatan',
                    'Unmeasurable'
                ];

            // Hapus instance lama jika ada
            const existingChart = Chart.getChart(canvasElement);
            if (existingChart) {
                existingChart.destroy();
            }

            const ctx = canvasElement.getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: efektifValues,
                        backgroundColor: [
                            '#22c55e', // Effective (Hijau)
                            '#84cc16', // Mostly Effective (Lime)
                            '#facc15', // Partially Effective (Kuning)
                            '#ef4444', // In-Effective (Merah)
                            '#94a3b8', // Pencatatan (Slate)
                            '#8b5cf6'  // Unmeasurable (Ungu)
                        ],
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { weight: 'normal' },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    let val = context.raw;
                                    let total = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = total > 0 ? Math.round((val / total) * 1000) / 10 + '%' : '0%';
                                    return label + val + ' (' + percentage + ')';
                                }
                            }
                        }
                    }
                }
            });
        }, 250);
    });
</script>
