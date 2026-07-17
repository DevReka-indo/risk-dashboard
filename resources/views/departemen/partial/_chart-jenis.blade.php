<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm mt-6">
    <h3 class="mb-4 text-base font-bold text-slate-900">Jumlah Risiko berdasarkan Jenis</h3>
    <div class="h-64">
        <canvas id="chartJenis"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delay 200ms untuk memastikan container ter-render dengan baik
        setTimeout(function() {
            const canvasElement = document.getElementById('chartJenis');
            if (!canvasElement) return;

            const ctx = canvasElement.getContext('2d');
            const jenisData = {!! json_encode($jenisRisikoData ?? [0, 0]) !!};

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Proyek', 'Non-Proyek'],
                    datasets: [{
                        data: jenisData,
                        backgroundColor: [
                            '#3274a1', // Biru Gelap (Proyek)
                            '#bad0e4'  // Biru Terang (Non-Proyek)
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
                                usePointStyle: true, // Membuat icon legend menjadi bulat
                            }
                        },
                        // Tooltip custom agar memunculkan format: Angka (Persentase%)
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
        }, 200);
    });
</script>
