<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm mt-6">
    <h3 class="mb-4 text-base font-bold text-slate-900">Keberhasilan Penanganan Risiko</h3>
    <div class="h-64">
        <canvas id="chartEfektif"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const canvasElement = document.getElementById('chartEfektif');
            if (!canvasElement) return;

            const ctx = canvasElement.getContext('2d');
            const efektifData = {!! json_encode($efektifRisikoData ?? [0, 0, 0, 0]) !!};

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [
                        'Effective',
                        'Mostly Effective',
                        'Partially Effective',
                        'In-Effective',
                        'Pencatatan',
                        'Unmeasurable'
                    ],
                    datasets: [{
                        data: efektifData,
                        backgroundColor: [
                            '#22c55e',
                            '#84cc16',
                            '#facc15',
                            '#ef4444',
                            '#94a3b8',
                            '#8b5cf6'
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
